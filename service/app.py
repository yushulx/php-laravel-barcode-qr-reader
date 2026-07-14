from flask import Flask, request, jsonify
import base64
import binascii
import os
import urllib.parse

from dynamsoft_capture_vision_bundle import (
    LicenseManager, CaptureVisionRouter, EnumPresetTemplate, EnumErrorCode
)

# Get a license key from https://www.dynamsoft.com/customer/license/trialLicense
LICENSE_KEY = "DLS2eyJoYW5kc2hha2VDb2RlIjoiMjAwMDAxLTE2NDk4Mjk3OTI2MzUiLCJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSIsInNlc3Npb25QYXNzd29yZCI6IndTcGR6Vm05WDJrcEQ5YUoifQ=="

# Initialize license once on startup
LicenseManager.init_license(LICENSE_KEY)

# Create CaptureVisionRouter instance for barcode detection
reader = CaptureVisionRouter()

app = Flask(__name__)


def decode_file_content(file_content):
    """Decode barcodes from file bytes.

    Returns a list of results, where each result is a list matching the
    original PHP C extension format:
    [barcode_format_string, barcode_text, raw_bytes_hex, localization_string]
    """
    output = []
    try:
        results = reader.capture_multi_pages(file_content, EnumPresetTemplate.PT_READ_BARCODES)
        result_list = results.get_results()
        for result in result_list:
            if result.get_error_code() != EnumErrorCode.EC_OK:
                continue

            items = result.get_items()
            for item in items:
                format_str = item.get_format_string()
                text = item.get_text()
                raw_bytes = item.get_bytes() or b""
                raw_hex = binascii.hexlify(raw_bytes).decode("utf-8")

                location = item.get_location()
                points = location.points if location else []
                localization_str = ""
                if len(points) >= 4:
                    localization_str = "[(%d,%d),(%d,%d),(%d,%d),(%d,%d)]" % (
                        points[0].x, points[0].y,
                        points[1].x, points[1].y,
                        points[2].x, points[2].y,
                        points[3].x, points[3].y,
                    )

                output.append([format_str, text, raw_hex, localization_str])
    except Exception as error:
        return {"error": str(error)}

    return output


@app.route("/decode", methods=["GET"])
def decode_by_path():
    """Decode barcodes from a file path.

    Example:
        GET /decode?file=C%3A%5Cpath%5Cto%5Cimage.png
    """
    file_path = request.args.get("file", "")
    if not file_path:
        return jsonify({"error": "Missing 'file' parameter"}), 400

    file_path = urllib.parse.unquote(file_path)
    if not os.path.exists(file_path):
        return jsonify({"error": "File not found: " + file_path}), 404

    try:
        with open(file_path, "rb") as f:
            file_content = f.read()
    except Exception as e:
        return jsonify({"error": "Failed to read file: " + str(e)}), 500

    output = decode_file_content(file_content)
    if isinstance(output, dict) and "error" in output:
        return jsonify(output), 500

    return jsonify(output)


@app.route("/decode", methods=["POST"])
def decode_by_upload():
    """Decode barcodes from an uploaded file or base64 body.

    Supports:
        - multipart/form-data with a 'file' field
        - raw base64 bytes in the request body
    """
    file_content = None

    request_body = request.data.decode("utf-8") if request.data else ""
    if request_body:
        try:
            base64_content = urllib.parse.unquote(request_body)
            file_content = base64.b64decode(base64_content)
        except Exception:
            return jsonify({"error": "Invalid base64 string"}), 400
    elif "file" in request.files:
        file = request.files["file"]
        if file.filename == "":
            return jsonify({"error": "Empty file"}), 400
        file_content = file.read()

    if file_content is None:
        return jsonify({"error": "No file uploaded"}), 400

    output = decode_file_content(file_content)
    if isinstance(output, dict) and "error" in output:
        return jsonify(output), 500

    return jsonify(output)


@app.route("/health", methods=["GET"])
def health():
    return jsonify({"status": "ok"})


if __name__ == "__main__":
    import sys

    host = "0.0.0.0" if "--host-all" in sys.argv else "127.0.0.1"
    port = int(os.environ.get("BARCODE_SERVICE_PORT", "8080"))
    print(f"Starting barcode service on http://{host}:{port}")
    app.run(host=host, port=port)
