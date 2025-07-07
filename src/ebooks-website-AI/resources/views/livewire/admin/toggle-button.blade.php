<div>
    <div>
        <label class="switch">
            <input
                type="checkbox"
                wire:model.live="active">
            <span class="slider round"></span>
        </label>

        <div
            x-data="{ 
                shown: false, 
                message: 'Data Saved' 
            }"
            x-show.transition.opacity.duration.1500ms="shown"
            x-init="
                @this.on('statusUpdated', (event) => {
                    message = event.message;
                    shown = true;
                    setTimeout(() => shown = false, 2000);
                })
            "
            x-text="message"
            class="font-italic mt-1"
            style="display: none"></div>
    </div>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 30px;
            height: 17px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #BB2D3B;
            -webkit-transition: .4s;
            transition: .4s,
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 13px;
            width: 13px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s,
        }

        input:checked+.slider {
            background-color: #68D391 !important;
        }

        input:focus+.slider {
            box-shadow: #68D391 !important;
            /* background-color: #000!important; */
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(13px);
            -ms-transform: translateX(13px);
            transform: translateX(13px);
        }

        /* Rounded slider */
        .slider.round {
            border-radius: 17px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>