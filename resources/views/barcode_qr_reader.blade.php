<!DOCTYPE html>
<html>

<head>
    <title>PHP Laravel Barcode QR Reader</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{csrf_token()}}" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 24px;
            font-size: 24px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            justify-content: center;
        }

        .drop-zone {
            border: 2px dashed #c0c4cc;
            border-radius: 8px;
            padding: 24px 32px;
            text-align: center;
            color: #606266;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s;
            background-color: #fff;
            min-width: 280px;
        }

        .drop-zone:hover,
        .drop-zone.drag-over {
            border-color: #409eff;
            background-color: #f0f9ff;
        }

        .drop-zone input {
            display: none;
        }

        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            background-color: #409eff;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn:hover:not(:disabled) {
            background-color: #66b1ff;
        }

        .btn:disabled {
            background-color: #a0cfff;
            cursor: not-allowed;
        }

        .main {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        @media (max-width: 900px) {
            .main {
                flex-direction: column;
            }
        }

        .viewer-wrapper {
            flex: 1 1 0;
            min-width: 0;
        }

        .viewer {
            position: relative;
            display: block;
            width: 100%;
            background-color: #fff;
            border: 1px solid #e4e7ed;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .viewer img,
        .viewer canvas {
            display: block;
            width: 100%;
            height: auto;
            max-height: 70vh;
            object-fit: contain;
        }

        .viewer canvas {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .results-panel {
            flex: 0 0 360px;
            width: 360px;
            max-width: 100%;
            background-color: #fff;
            border: 1px solid #e4e7ed;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        @media (max-width: 900px) {
            .results-panel {
                flex: 1 1 auto;
                width: 100%;
            }
        }

        .results-panel h3 {
            margin-top: 0;
            font-size: 16px;
        }

        #resultText {
            width: 100%;
            height: 200px;
            resize: vertical;
            border: 1px solid #dcdfe6;
            border-radius: 4px;
            padding: 10px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.5;
        }

        .result-list {
            margin-top: 12px;
            max-height: 300px;
            overflow-y: auto;
        }

        .result-item {
            padding: 10px;
            border-bottom: 1px solid #ebeef5;
            font-size: 13px;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-format {
            font-weight: bold;
            color: #409eff;
        }

        .status {
            margin-top: 12px;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            display: none;
        }

        .status.error {
            display: block;
            background-color: #fef0f0;
            color: #f56c6c;
            border: 1px solid #fde2e2;
        }

        .status.success {
            display: block;
            background-color: #f0f9eb;
            color: #67c23a;
            border: 1px solid #e1f3d8;
        }

        .placeholder {
            color: #909399;
            text-align: center;
            padding: 80px 20px;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>PHP Laravel Barcode QR Reader</h1>

        <div class="toolbar">
            <div class="drop-zone" id="dropZone">
                <div>Drag & drop an image here or click to select</div>
                <input type="file" id="BarcodeQrImage" accept="image/*">
            </div>
            <button class="btn" id="readBtn" disabled>Read Barcode</button>
        </div>

        <div class="main">
            <div class="viewer-wrapper">
                <div class="viewer" id="viewer">
                    <div class="placeholder" id="placeholder">No image selected</div>
                    <img id="image" alt="Uploaded barcode image" style="display: none;">
                    <canvas id="overlay" style="display: none;"></canvas>
                </div>
            </div>

            <div class="results-panel">
                <h3>Decoded Results</h3>
                <textarea id="resultText" placeholder="Barcode results will appear here..."></textarea>
                <div class="result-list" id="resultList"></div>
                <div class="status" id="status"></div>
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('BarcodeQrImage');
        const readBtn = document.getElementById('readBtn');
        const viewer = document.getElementById('viewer');
        const placeholder = document.getElementById('placeholder');
        const image = document.getElementById('image');
        const overlay = document.getElementById('overlay');
        const resultText = document.getElementById('resultText');
        const resultList = document.getElementById('resultList');
        const status = document.getElementById('status');
        const token = document.querySelector('meta[name="_token"]').getAttribute('content');

        let currentFile = null;

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                handleFile(fileInput.files[0]);
            }
        });

        function handleFile(file) {
            currentFile = file;
            readBtn.disabled = false;
            setStatus('', '');

            const reader = new FileReader();
            reader.onload = (e) => {
                image.src = e.target.result;
                image.style.display = 'block';
                placeholder.style.display = 'none';
                clearOverlay();
            };
            reader.readAsDataURL(file);
        }

        readBtn.addEventListener('click', () => {
            if (!currentFile) return;
            decodeImage(currentFile);
        });

        image.addEventListener('load', () => {
            resizeOverlay();
        });

        window.addEventListener('resize', () => {
            resizeOverlay();
        });

        function resizeOverlay() {
            if (image.naturalWidth === 0) return;
            overlay.width = image.naturalWidth;
            overlay.height = image.naturalHeight;
            overlay.style.display = 'block';
        }

        function clearOverlay() {
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);
        }

        function drawOverlay(results) {
            clearOverlay();
            const ctx = overlay.getContext('2d');
            const colors = ['#409eff', '#67c23a', '#e6a23c', '#f56c6c', '#909399', '#8e44ad'];

            results.forEach((result, index) => {
                const points = parseLocalization(result.localization);
                if (points.length < 4) return;

                const color = colors[index % colors.length];

                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);
                for (let i = 1; i < points.length; i++) {
                    ctx.lineTo(points[i].x, points[i].y);
                }
                ctx.closePath();
                ctx.lineWidth = Math.max(2, overlay.width / 300);
                ctx.strokeStyle = color;
                ctx.stroke();

                ctx.fillStyle = color;
                ctx.font = `bold ${Math.max(12, overlay.width / 80)}px sans-serif`;
                const label = result.format + ': ' + result.text;
                ctx.fillText(label, points[0].x, Math.max(points[0].y - 6, 16));
            });
        }

        function parseLocalization(str) {
            const points = [];
            const regex = /\((-?\d+),\s*(-?\d+)\)/g;
            let match;
            while ((match = regex.exec(str)) !== null) {
                points.push({ x: parseInt(match[1], 10), y: parseInt(match[2], 10) });
            }
            return points;
        }

        function decodeImage(file) {
            readBtn.disabled = true;
            readBtn.textContent = 'Reading...';
            resultText.value = '';
            resultList.innerHTML = '';
            setStatus('', '');
            clearOverlay();

            const formData = new FormData();
            formData.append('_token', token);
            formData.append('BarcodeQrImage', file);

            fetch('{{ route('image.upload') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error((data.message || ['Upload failed']).join('\n'));
                    }).catch(() => {
                        throw new Error('Upload failed with status ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                readBtn.disabled = false;
                readBtn.textContent = 'Read Barcode';

                if (!data.success) {
                    setStatus('error', Array.isArray(data.message) ? data.message.join('\n') : data.message);
                    return;
                }

                setStatus('success', data.message);

                if (data.results && data.results.length > 0) {
                    resultText.value = data.results.map(r => {
                        return `[${r.format}] ${r.text}\nLocalization: ${r.localization}\nRaw: ${r.raw}`;
                    }).join('\n\n');

                    resultList.innerHTML = data.results.map(r => {
                        return `<div class="result-item">
                            <div><span class="result-format">${escapeHtml(r.format)}</span></div>
                            <div><strong>Text:</strong> ${escapeHtml(r.text)}</div>
                            <div><strong>Localization:</strong> ${escapeHtml(r.localization)}</div>
                        </div>`;
                    }).join('');

                    drawOverlay(data.results);
                } else {
                    resultText.value = 'No barcode found.';
                }
            })
            .catch(error => {
                readBtn.disabled = false;
                readBtn.textContent = 'Read Barcode';
                setStatus('error', error.message);
            });
        }

        function setStatus(type, message) {
            status.className = 'status' + (type ? ' ' + type : '');
            status.textContent = message;
            status.style.display = type ? 'block' : 'none';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>
