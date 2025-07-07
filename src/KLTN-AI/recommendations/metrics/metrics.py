import sys
import numpy as np
import logging

logging.basicConfig(stream=sys.stdout, level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def calculate_precision_recall_ndcg(recommended_items: list, relevant_items: list, k: int):
    """
    Tính Precision@k, Recall@k, và NDCG@k cho một danh sách gợi ý.
    - recommended_items: List các product_id được gợi ý (top-k).
    - relevant_items: List các product_id thực sự phù hợp từ tập kiểm tra.
    - k: Số lượng gợi ý cần đánh giá.
    """
    # Precision@k
    recommended_k = recommended_items[:k]
    relevant_set = set(relevant_items)
    true_positives = len([item for item in recommended_k if item in relevant_set])
    precision = true_positives / k if k > 0 else 0

    # Recall@k
    recall = true_positives / len(relevant_set) if relevant_set else 0

    # NDCG@k
    def dcg_at_k(recommended, relevant_set):
        dcg = 0
        for i, item in enumerate(recommended[:k]):
            if item in relevant_set:
                dcg += 1 / np.log2(i + 2)  # i+2 để tránh log(1)
        return dcg

    def idcg_at_k(n_relevant):
        # Tính IDCG (DCG lý tưởng)
        return sum(1 / np.log2(i + 2) for i in range(min(n_relevant, k)))

    dcg = dcg_at_k(recommended_k, relevant_set)
    idcg = idcg_at_k(len(relevant_set))
    ndcg = dcg / idcg if idcg > 0 else 0

    logging.debug(f"Precision@{k}: {precision}, Recall@{k}: {recall}, NDCG@{k}: {ndcg}")
    return precision, recall, ndcg

def calculate_diversity(recommended_items: list, content_similarity: np.ndarray, product_id_to_content_idx: dict, k: int):
    """
    Tính độ đa dạng (Intra-List Diversity) của danh sách gợi ý.
    - recommended_items: List các product_id được gợi ý.
    - content_similarity: Ma trận tương đồng nội dung.
    - product_id_to_content_idx: Ánh xạ từ product_id sang chỉ số trong ma trận content_similarity.
    - k: Số lượng gợi ý cần đánh giá.
    """
    scores = []
    for i in range(len(recommended_items[:k])):
        for j in range(i + 1, len(recommended_items[:k])):
            pid1, pid2 = recommended_items[i], recommended_items[j]
            idx1 = product_id_to_content_idx.get(pid1, -1)
            idx2 = product_id_to_content_idx.get(pid2, -1)
            if idx1 >= 0 and idx2 >= 0 and idx1 < content_similarity.shape[0] and idx2 < content_similarity.shape[0]:
                similarity = content_similarity[idx1, idx2]
                scores.append(1 - similarity)  # Càng ít tương đồng, càng đa dạng
    diversity = np.mean(scores) if scores else 0
    logging.debug(f"Diversity@{k}: {diversity}")
    return diversity

def calculate_coverage_at_k(recommendations_dict: dict, total_items: set, k: int) -> float:
    """
    Tính Coverage@K cho toàn bộ hệ thống gợi ý.
    - recommendations_dict: Dictionary với key là user_id, value là list các product_id được gợi ý.
    - total_items: Set chứa tất cả product_id trong danh mục.
    - k: Số lượng gợi ý cần đánh giá.
    """
    unique_recommended = set()
    for user_id, recommended_items in recommendations_dict.items():
        recommended_k = recommended_items[:k]
        unique_recommended.update(recommended_k)
    
    coverage = len(unique_recommended) / len(total_items) if total_items else 0
    logging.info(f"Coverage@{k}: {coverage:.4f} ({len(unique_recommended)}/{len(total_items)})")
    return coverage