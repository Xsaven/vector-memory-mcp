#!/bin/bash
# Launch script for Vector Memory MCP Server on Apple Silicon

# Ensure we're running on arm64 architecture
if [[ $(arch) != "arm64" ]]; then
    echo "Switching to arm64 architecture..."
    exec arch -arm64 "$0" "$@"
fi

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Check if venv exists and has correct Python
if [[ ! -d "$SCRIPT_DIR/.venv" ]]; then
    echo "Creating virtual environment with arm64 Python..."
    /Users/xsaven/miniconda3/envs/vector-mcp/bin/python -m venv "$SCRIPT_DIR/.venv"
    echo "Installing dependencies..."
    "$SCRIPT_DIR/.venv/bin/pip" install -r "$SCRIPT_DIR/requirements.txt"
fi

# Run the server
echo "Starting Vector Memory MCP Server (arm64 mode)..."
exec "$SCRIPT_DIR/.venv/bin/python" "$SCRIPT_DIR/main.py" "$@"