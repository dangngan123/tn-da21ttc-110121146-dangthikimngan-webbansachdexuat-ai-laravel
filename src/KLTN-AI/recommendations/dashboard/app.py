import os
import sys
import logging
import requests
import pandas as pd
import pickle
import plotly.express as px
from dash import Dash, html, dcc, callback, Output, Input, State, no_update
import dash_bootstrap_components as dbc
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry
import glob
import shutil
import json

logging.basicConfig(stream=sys.stdout, level=logging.INFO,
                    format='%(asctime)s - %(levelname)s - %(message)s')

API_BASE_URL = os.getenv("API_URL", "http://localhost:8001")
session = requests.Session()
retries = Retry(total=3, backoff_factor=1, status_forcelist=[500, 502, 503, 504])
session.mount("http://", HTTPAdapter(max_retries=retries))

def check_api_status():
    try:
        response = session.get(f"{API_BASE_URL}/", timeout=5)
        response.raise_for_status()
        logging.info("API sẵn sàng")
        return True
    except Exception as e:
        logging.error(f"API không khả dụng: {e}")
        return False

if not check_api_status():
    print("Cảnh báo: API không khả dụng. Vui lòng khởi động API tại", API_BASE_URL)

app = Dash(__name__, external_stylesheets=[dbc.themes.COSMO], suppress_callback_exceptions=True)

app.layout = dbc.Container([
    html.H1("Dashboard Gợi Ý Sách", className="text-center my-4", style={'color': '#2c3e50'}),
    dbc.Alert(id="global-alert", is_open=False, dismissable=True, duration=5000, className="mb-4"),
    dcc.Tabs([
        dcc.Tab(label="Gợi Ý Sách", children=[
            dbc.Card([
                dbc.CardHeader("Gợi Ý Sách Cho Người Dùng", className="h5"),
                dbc.CardBody([
                    dbc.Row([
                        dbc.Col([
                            dbc.Label("User ID", html_for="user-id-input"),
                            dcc.Input(id="user-id-input", type="number", placeholder="Nhập User ID (ví dụ: 1)",
                                      value=1, className="form-control", min=1),
                        ], width=4),
                        dbc.Col([
                            dbc.Label("Phương thức gợi ý", html_for="method-dropdown"),
                            dcc.Dropdown(
                                id="method-dropdown",
                                options=[
                                    {'label': 'Hybrid (SVD + Content + ALS)', 'value': 'hybrid'},
                                    {'label': 'ALS User-based', 'value': 'als_user'},
                                    {'label': 'ALS Item-based', 'value': 'als_item'},
                                    {'label': 'SVD User-based', 'value': 'user_based_svd'},
                                    {'label': 'SVD Item-based', 'value': 'item_based_svd'},
                                    {'label': 'Content-based', 'value': 'content_based'},
                                ],
                                value='hybrid',
                                className="form-select"
                            ),
                        ], width=4),
                        dbc.Col([
                            dbc.Label("Số lượng gợi ý", html_for="n-items-input"),
                            dcc.Input(id="n-items-input", type="number", placeholder="Số lượng (1-50)",
                                      value=10, className="form-control", min=1, max=50),
                        ], width=4),
                    ], className="mb-3"),
                    dbc.Row([
                        dbc.Col([
                            dbc.Label("Alpha (User-based SVD)", html_for="alpha-input"),
                            dcc.Input(id="alpha-input", type="number", placeholder="0.4", value=0.4,
                                      className="form-control", min=0, max=1, step=0.05),
                        ], width=2),
                        dbc.Col([
                            dbc.Label("Beta (Item-based SVD)", html_for="beta-input"),
                            dcc.Input(id="beta-input", type="number", placeholder="0.3", value=0.3,
                                      className="form-control", min=0, max=1, step=0.05),
                        ], width=2),
                        dbc.Col([
                            dbc.Label("Gamma (Content-based)", html_for="gamma-input"),
                            dcc.Input(id="gamma-input", type="number", placeholder="0.15", value=0.15,
                                      className="form-control", min=0, max=1, step=0.05),
                        ], width=2),
                        dbc.Col([
                            dbc.Label("Delta (ALS User)", html_for="delta-input"),
                            dcc.Input(id="delta-input", type="number", placeholder="0.1", value=0.1,
                                      className="form-control", min=0, max=1, step=0.05),
                        ], width=2),
                        dbc.Col([
                            dbc.Label("Epsilon (ALS Item)", html_for="epsilon-input"),
                            dcc.Input(id="epsilon-input", type="number", placeholder="0.05", value=0.05,
                                      className="form-control", min=0, max=1, step=0.05),
                        ], width=2),
                    ], className="mb-3"),
                    dbc.Button(
                        "Lấy Gợi Ý", id="get-recs-button", n_clicks=0,
                        color="primary", className="me-2", title="Lấy danh sách sách gợi ý"
                    ),
                    dcc.Loading(
                        dcc.Graph(id="recommendation-chart"),
                        type="circle", className="my-3"
                    ),
                    html.Div(id="recs-error-message", className="text-danger"),
                    html.Div(id="weight-warning", className="text-warning"),
                ])
            ], className="mb-4")
        ]),
        dcc.Tab(label="Tra Cứu Sản Phẩm", children=[
            dbc.Card([
                dbc.CardHeader("Tra Cứu Thông Tin Sách", className="h5"),
                dbc.CardBody([
                    dbc.Label("Chọn sách", html_for="product-dropdown"),
                    dcc.Dropdown(
                        id="product-dropdown",
                        options=[],
                        placeholder="Chọn sách để xem chi tiết",
                        className="form-select mb-3"
                    ),
                    dcc.Loading(
                        html.Div(id="product-details-output"),
                        type="circle", className="my-3"
                    ),
                    html.Div(id="product-error-message", className="text-danger")
                ])
            ], className="mb-4")
        ]),
        dcc.Tab(label="Đánh Giá Mô Hình", children=[
            dbc.Card([
                dbc.CardHeader("Đánh Giá Hiệu Suất Mô Hình", className="h5"),
                dbc.CardBody([
                    dbc.Row([
                        dbc.Col([
                            dbc.Label("Nhập k", html_for="k-input"),
                            dcc.Input(id="k-input", type="number", placeholder="Nhập k (ví dụ: 10)",
                                      value=10, className="form-control", min=1, max=50),
                        ], width=4),
                    ], className="mb-3"),
                    dbc.Button(
                        "Đánh Giá", id="evaluate-button", n_clicks=0,
                        color="primary", className="me-2", title="Đánh giá hiệu suất mô hình"
                    ),
                    dcc.Loading(
                        dcc.Graph(id="evaluation-chart"),
                        type="circle", className="my-3"
                    ),
                    html.Div(id="evaluation-error-message", className="text-danger")
                ])
            ], className="mb-4")
        ]),
        dcc.Tab(label="Heatmap Tương Tác", children=[
            dbc.Card([
                dbc.CardHeader("Heatmap Tương Tác User-Item", className="h5"),
                dbc.CardBody([
                    dcc.Loading(
                        dcc.Graph(id="interaction-heatmap"),
                        type="circle", className="my-3"
                    ),
                    html.Div(id="heatmap-error-message", className="text-danger")
                ])
            ], className="mb-4")
        ]),
        dcc.Tab(label="Quản Lý Huấn Luyện", children=[
            dbc.Card([
                dbc.CardHeader("Quản Lý Huấn Luyện & Mô Hình", className="h5"),
                dbc.CardBody([
                    dbc.Row([
                        dbc.Col([
                            dbc.Button(
                                "Huấn Luyện Ngay", id="train-button", n_clicks=0,
                                color="success", className="mb-3 me-2", title="Kích hoạt huấn luyện mô hình ALS"
                            ),
                            dbc.Button(
                                "Xóa Cache Gợi Ý", id="clear-cache-button", n_clicks=0,
                                color="warning", className="mb-3", title="Xóa cache gợi ý trong Redis"
                            ),
                            dbc.Button(
                                "Chọn Mô Hình", id="select-model-button", n_clicks=0,
                                color="info", className="mb-3 me-2", title="Chọn mô hình từ danh sách"
                            ),
                            dbc.Button(
                                "Xóa Mô Hình", id="delete-model-button", n_clicks=0,
                                color="danger", className="mb-3", title="Xóa mô hình hiện tại"
                            ),
                        ], width=6),
                        dbc.Col([
                            html.Div(id="training-schedule", className="text-info mb-3",
                                     children="Đang tải lịch trình huấn luyện..."),
                            dbc.Button(
                                "Xem Lịch Huấn Luyện", id="view-schedule-button", n_clicks=0,
                                color="info", className="mb-3", title="Xem lịch trình huấn luyện tự động"
                            ),
                            dcc.Dropdown(
                                id="model-dropdown",
                                options=[],
                                placeholder="Chọn mô hình...",
                                className="form-select mb-3"
                            ),
                            html.Div(id="model-status", className="text-info mb-3",
                                     children="Đang tải trạng thái mô hình..."),
                        ], width=6),
                    ]),
                    dbc.Progress(id="training-progress", value=0, striped=True, animated=True, className="mb-3"),
                    html.Div(id="training-status", className="text-info"),
                    html.Div(id="training-error-message", className="text-danger")
                ])
            ], className="mb-4")
        ])
    ]),
    dcc.Interval(id="interval-component-product-list", interval=10*60*1000, n_intervals=0),
    dcc.Interval(id="interval-component-training", interval=10*1000, n_intervals=0),
    dcc.Interval(id="interval-component-model-status", interval=5*1000, n_intervals=0),
], fluid=True)

@app.callback(
    [Output("recommendation-chart", "figure"), Output("recs-error-message", "children"),
     Output("global-alert", "children"), Output("global-alert", "is_open"), Output("weight-warning", "children")],
    Input("get-recs-button", "n_clicks"),
    [State("user-id-input", "value"), State("method-dropdown", "value"), State("n-items-input", "value"),
     State("alpha-input", "value"), State("beta-input", "value"), State("gamma-input", "value"),
     State("delta-input", "value"), State("epsilon-input", "value")],
    prevent_initial_call=True
)
def update_recommendation_chart(n_clicks, user_id, method, n_items, alpha, beta, gamma, delta, epsilon):
    if n_clicks == 0 or user_id is None or method is None or n_items is None:
        return px.bar(title="Vui lòng nhập thông tin và nhấn 'Lấy Gợi Ý'"), "", "", False, ""

    alpha = float(alpha) if alpha is not None else 0.4
    beta = float(beta) if beta is not None else 0.3
    gamma = float(gamma) if gamma is not None else 0.15
    delta = float(delta) if delta is not None else 0.1
    epsilon = float(epsilon) if epsilon is not None else 0.05

    total_weight = alpha + beta + gamma + delta + epsilon
    weight_warning = ""
    if abs(total_weight - 1.0) > 0.01:
        weight_warning = f"Cảnh báo: Tổng trọng số ({total_weight:.2f}) không bằng 1.0, có thể ảnh hưởng kết quả!"

    payload = {
        "user_id": int(user_id),
        "n_items": int(n_items),
        "method": method,
        "alpha": alpha,
        "beta": beta,
        "gamma": gamma,
        "delta": delta,
        "epsilon": epsilon
    }
    try:
        response = session.post(f"{API_BASE_URL}/recommend", json=payload, timeout=20)
        response.raise_for_status()
        data = response.json()

        if not data:
            return px.bar(title=f"Không có gợi ý cho User {user_id} ({method})"), "Không tìm thấy gợi ý.", "Không có gợi ý.", True, weight_warning

        df = pd.DataFrame(data)
        fig = px.bar(
            df,
            x="name",
            y="score",
            labels={"name": "Tên Sách", "score": "Điểm Dự Đoán"},
            title=f"Top {len(data)} Sách Gợi Ý cho User {user_id} (Hybrid, α={alpha:.2f}, β={beta:.2f}, γ={gamma:.2f}, δ={delta:.2f}, ε={epsilon:.2f})",
            color_discrete_sequence=["#36A2EB"],
        )
        fig.update_traces(
            hovertemplate="%{x}<br>Điểm Dự Đoán: %{y:.4f}<extra></extra>",
            marker=dict(line=dict(color="#36A2EB", width=1))
        )
        fig.update_layout(
            xaxis_tickangle=45,
            yaxis=dict(title="Điểm Dự Đoán", range=[0, max(df["score"]) * 1.1]),
            template="plotly_white",
            showlegend=True,
            margin=dict(b=150),
        )
        logging.info(f"Đã cập nhật gợi ý cho user {user_id}, phương thức {method} với trọng số: α={alpha}, β={beta}, γ={gamma}, δ={delta}, ε={epsilon}")
        return fig, "", f"Lấy gợi ý thành công cho User {user_id}!", True, weight_warning

    except requests.exceptions.Timeout:
        logging.error(f"Timeout khi lấy gợi ý cho user {user_id}")
        return px.bar(title="Lỗi: Timeout"), "Yêu cầu API bị timeout.", "Lỗi: Timeout.", True, weight_warning
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP: {error_detail}")
        return px.bar(title="Lỗi API"), f"Lỗi API: {error_detail}", f"Lỗi API: {error_detail}", True, weight_warning
    except Exception as e:
        logging.error(f"Lỗi không xác định: {str(e)}")
        return px.bar(title="Lỗi"), f"Lỗi: {str(e)}", f"Lỗi: {str(e)}", True, weight_warning

@app.callback(
    Output("product-dropdown", "options"),
    Input("interval-component-product-list", "n_intervals")
)
def update_product_dropdown_options(n_intervals):
    try:
        response = session.get(f"{API_BASE_URL}/products/list", timeout=10)
        response.raise_for_status()
        products = response.json()
        options = [{"label": f"{p['name']} (ID: {p['id']})", "value": p['id']} for p in products]
        logging.info("Đã cập nhật danh sách sản phẩm")
        return options if options else no_update
    except requests.exceptions.RequestException as e:
        logging.error(f"Lỗi tải danh sách sản phẩm: {e}")
        return no_update

@app.callback(
    [Output("product-details-output", "children"), Output("product-error-message", "children"),
     Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("product-dropdown", "value"),
    prevent_initial_call=True
)
def display_product_details(product_id):
    if not product_id:
        return html.Div("Vui lòng chọn một sản phẩm."), "", "", False

    try:
        response = session.get(f"{API_BASE_URL}/products/{product_id}", timeout=10)
        response.raise_for_status()
        product = response.json()

        details = [
            html.H3(product.get("name", "N/A"), style={"color": "#007bff"}),
            html.P([html.Strong("ID: "), str(product.get("id", "N/A"))]),
        ]
        if product.get("image"):
            details.append(html.Img(src=product.get("image"), style={"maxWidth": "200px", "maxHeight": "200px", "border": "1px solid #ddd", "margin": "10px 0"}))

        fields = {
            "Tác giả": "author", "Nhà xuất bản": "publisher", "Mô tả ngắn": "short_description",
            "Giá gốc": "reguler_price", "Giá bán": "sale_price", "Đã bán": "sold_count", "Tồn kho": "quantity"
        }
        for label, key in fields.items():
            if value := product.get(key):
                details.append(html.P([html.Strong(f"{label}: "), str(value)]))

        logging.info(f"Đã hiển thị chi tiết sản phẩm {product_id}")
        return html.Div(details), "", f"Hiển thị chi tiết sản phẩm {product.get('name', 'N/A')}", True

    except requests.exceptions.Timeout:
        logging.error(f"Timeout khi lấy chi tiết sản phẩm {product_id}")
        return "", "Yêu cầu API bị timeout.", "Lỗi: Timeout.", True
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP: {error_detail}")
        return "", f"Lỗi API: {error_detail}", f"Lỗi API: {error_detail}", True
    except Exception as e:
        logging.error(f"Lỗi: {str(e)}")
        return "", f"Lỗi: {str(e)}", f"Lỗi: {str(e)}", True

@app.callback(
    [Output("evaluation-chart", "figure"), Output("evaluation-error-message", "children"),
     Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("evaluate-button", "n_clicks"),
    State("k-input", "value"),
    prevent_initial_call=True
)
def update_evaluation_chart(n_clicks, k):
    if n_clicks == 0 or k is None:
        return px.bar(title="Vui lòng nhập k và nhấn 'Đánh Giá'"), "", "", False

    try:
        response = session.get(f"{API_BASE_URL}/metrics/evaluate_model?k={k}", timeout=10)
        response.raise_for_status()
        metrics = response.json()

        if not metrics:
            return px.bar(title="Không có dữ liệu đánh giá"), "Không có dữ liệu đánh giá.", "Không có dữ liệu.", True

        df = pd.DataFrame(metrics)
        fig = px.bar(
            df,
            x="method",
            y=["precision_at_k", "recall_at_k", "ndcg_at_k", "diversity_at_k", "coverage_at_k"],
            barmode="group",
            title=f"So Sánh Hiệu Suất Các Phương Thức Gợi Ý (k={k})",
            labels={"value": "Giá Trị", "method": "Phương Thức", "variable": "Chỉ Số"},
            color_discrete_sequence=["#36A2EB", "#FF6384", "#4BC0C0", "#9966FF", "#FFCE56"],
        )
        fig.update_traces(
            hovertemplate="%{x}<br>%{yaxis.title.text}: %{y:.4f}<extra></extra>",
            marker=dict(line=dict(width=1))
        )
        fig.update_layout(
            xaxis_tickangle=45,
            yaxis=dict(title="Giá Trị", range=[0, 1]),
            legend_title="Chỉ Số",
            template="plotly_white",
            margin=dict(b=150),
        )
        logging.info(f"Đã cập nhật đánh giá cho k={k}")
        return fig, "", f"Đánh giá mô hình thành công với k={k}", True

    except requests.exceptions.Timeout:
        logging.error(f"Timeout khi đánh giá")
        return px.bar(title="Lỗi: Timeout"), "Yêu cầu API bị timeout.", "Lỗi: Timeout.", True
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP: {error_detail}")
        return px.bar(title="Lỗi API"), f"Lỗi API: {error_detail}", f"Lỗi API: {error_detail}", True
    except Exception as e:
        logging.error(f"Lỗi: {str(e)}")
        return px.bar(title="Lỗi"), f"Lỗi: {str(e)}", f"Lỗi: {str(e)}", True

@app.callback(
    [Output("interaction-heatmap", "figure"), Output("heatmap-error-message", "children")],
    Input("interval-component-product-list", "n_intervals")
)
def update_interaction_heatmap(n_intervals):
    try:
        response = session.get(f"{API_BASE_URL}/metrics/interaction_matrix", timeout=10)
        response.raise_for_status()
        data = response.json()

        users = data["users"]
        items = data["items"]
        values = data["values"]

        fig = px.imshow(
            values,
            labels=dict(x="Sản Phẩm", y="Người Dùng", color="Giá trị Tương Tác"),
            x=items,
            y=users,
            title="Heatmap Tương Tác User-Item",
            color_continuous_scale="Viridis"
        )
        fig.update_layout(
            xaxis_title="Product ID",
            yaxis_title="User ID",
            template="plotly_white",
            height=600
        )
        logging.info("Đã cập nhật heatmap tương tác")
        return fig, ""

    except requests.exceptions.RequestException as e:
        logging.error(f"Lỗi tải dữ liệu heatmap: {e}")
        return px.imshow([[0]], title="Lỗi: Không tải được dữ liệu"), f"Lỗi: {str(e)}"

@app.callback(
    [Output("training-status", "children"), Output("training-error-message", "children"),
     Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("train-button", "n_clicks"),
    prevent_initial_call=True
)
def trigger_training(n_clicks):
    if n_clicks == 0:
        return "", "", "", False

    try:
        response = session.post(f"{API_BASE_URL}/train", timeout=10)
        response.raise_for_status()
        logging.info("Đã kích hoạt huấn luyện mô hình")
        return "Huấn luyện đã được kích hoạt.", "", "Huấn luyện mô hình đã bắt đầu!", True
    except requests.exceptions.Timeout:
        logging.error("Timeout khi kích hoạt huấn luyện")
        return "", "Yêu cầu API bị timeout.", "Lỗi: Timeout.", True
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP khi kích hoạt huấn luyện: {error_detail}")
        return "", f"Lỗi API: {error_detail}", f"Lỗi API: {error_detail}", True
    except Exception as e:
        logging.error(f"Lỗi không xác định khi kích hoạt huấn luyện: {str(e)}")
        return "", f"Lỗi: {str(e)}", f"Lỗi: {str(e)}", True

@app.callback(
    [Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("clear-cache-button", "n_clicks"),
    prevent_initial_call=True
)
def clear_cache(n_clicks):
    if n_clicks == 0:
        return "", False

    try:
        response = session.post(f"{API_BASE_URL}/clear_cache", timeout=10)
        response.raise_for_status()
        message = response.json().get("message", "Cache đã được xóa")
        logging.info("Đã xóa cache gợi ý")
        return message, True
    except requests.exceptions.Timeout:
        logging.error("Timeout khi xóa cache")
        return "Lỗi: Timeout.", True
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP khi xóa cache: {error_detail}")
        return f"Lỗi API: {error_detail}", True
    except Exception as e:
        logging.error(f"Lỗi khi xóa cache: {str(e)}")
        return f"Lỗi: {str(e)}", True

@app.callback(
    [Output("training-schedule", "children"), Output("global-alert", "children", allow_duplicate=True),
     Output("global-alert", "is_open", allow_duplicate=True)],
    Input("view-schedule-button", "n_clicks"),
    prevent_initial_call=True
)
def view_training_schedule(n_clicks):
    if n_clicks == 0:
        return "Chưa tải lịch trình", "", False

    try:
        response = session.get(f"{API_BASE_URL}/training/schedule", timeout=10)
        response.raise_for_status()
        schedule = response.json()
        next_run = schedule.get("next_run", "N/A")
        trigger = schedule.get("trigger", "N/A")
        message = f"Lịch trình huấn luyện: {trigger}. Lần chạy tiếp theo: {next_run}"
        logging.info("Đã tải lịch trình huấn luyện")
        return message, "Đã tải lịch trình huấn luyện", True
    except requests.exceptions.Timeout:
        logging.error("Timeout khi lấy lịch trình huấn luyện")
        return "Lỗi: Timeout.", "Lỗi: Timeout.", True
    except requests.exceptions.HTTPError as http_err:
        error_detail = http_err.response.json().get("detail", str(http_err)) if http_err.response else str(http_err)
        logging.error(f"Lỗi HTTP khi lấy lịch trình: {error_detail}")
        return f"Lỗi API: {error_detail}", f"Lỗi API: {error_detail}", True
    except Exception as e:
        logging.error(f"Lỗi khi lấy lịch trình: {str(e)}")
        return f"Lỗi: {str(e)}", f"Lỗi: {str(e)}", True

@app.callback(
    [Output("training-progress", "value"), Output("training-status", "children", allow_duplicate=True),
     Output("interval-component-training", "disabled")],
    Input("interval-component-training", "n_intervals"),
    State("training-progress", "value"),
    prevent_initial_call=True
)
def update_training_progress(n_intervals, current_progress):
    try:
        response = session.get(f"{API_BASE_URL}/training/status", timeout=5)
        response.raise_for_status()
        progress_text = response.text.strip()
        progress_value = float(progress_text.strip("%")) if "%" in progress_text else 0
        if progress_value >= 100:
            return 100, "Huấn luyện hoàn tất!", True
        elif progress_value > 0:
            return progress_value, f"Đang huấn luyện: {progress_text}", False
        else:
            return 0, "Chưa bắt đầu huấn luyện", False
    except requests.exceptions.HTTPError as http_err:
        if http_err.response.status_code == 404:
            logging.warning("Endpoint /training/status không tồn tại, trả về tiến trình 0%")
        else:
            logging.error(f"Lỗi HTTP khi lấy tiến trình huấn luyện: {http_err}")
        return 0, "Lỗi: Không thể lấy trạng thái", False
    except Exception as e:
        logging.error(f"Lỗi lấy tiến trình huấn luyện: {e}")
        return 0, f"Lỗi: {str(e)}", False

@app.callback(
    [Output("model-dropdown", "options"), Output("model-status", "children"),
     Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("interval-component-model-status", "n_intervals"),
    prevent_initial_call=True
)
def update_model_status(n_intervals):
    model_dir = os.getenv("MODEL_SAVE_DIR", "./models")
    model_files = glob.glob(os.path.join(model_dir, "als_model_v*.pkl"))
    if model_files:
        options = [{"label": os.path.basename(f), "value": f} for f in model_files]
        latest_model = max(model_files, key=os.path.getmtime)
        with open(latest_model, "rb") as f:
            model_data = pickle.load(f)
        model_info = f"Mô hình hiện tại: {os.path.basename(latest_model)} (User IDs: {len(model_data['user_ids'])}, Item IDs: {len(model_data['item_ids'])})"
        return options, model_info, "", False
    return [], "Không có mô hình nào", "Không tìm thấy mô hình.", True

@app.callback(
    [Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("select-old-model-button", "n_clicks"),
    State("model-dropdown", "value"),
    prevent_initial_call=True
)
def select_old_model(n_clicks, selected_model):
    if n_clicks == 0 or not selected_model:
        return "Vui lòng chọn mô hình từ danh sách.", True

    try:
        response = session.post(f"{API_BASE_URL}/select_model", json={"model_path": selected_model}, timeout=10)
        response.raise_for_status()
        message = f"Đã chọn mô hình: {os.path.basename(selected_model)}"
        logging.info(message)
        return message, True
    except requests.exceptions.RequestException as e:
        logging.error(f"Lỗi khi chọn mô hình: {e}")
        return f"Lỗi: {str(e)}", True

@app.callback(
    [Output("global-alert", "children", allow_duplicate=True), Output("global-alert", "is_open", allow_duplicate=True)],
    Input("delete-model-button", "n_clicks"),
    State("model-dropdown", "value"),
    prevent_initial_call=True
)
def delete_model(n_clicks, selected_model):
    if n_clicks == 0 or not selected_model:
        return "Vui lòng chọn mô hình để xóa.", True

    try:
        os.remove(selected_model)
        metadata_file = selected_model.replace(".pkl", "_metadata.json")
        if os.path.exists(metadata_file):
            os.remove(metadata_file)
        logging.info(f"Đã xóa mô hình: {os.path.basename(selected_model)}")
        return f"Đã xóa mô hình: {os.path.basename(selected_model)}", True
    except Exception as e:
        logging.error(f"Lỗi khi xóa mô hình: {e}")
        return f"Lỗi: {str(e)}", True

if __name__ == "__main__":
    app.run(debug=True)