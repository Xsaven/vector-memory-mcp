# Publishing to PyPI

## Prerequisites

1. **uv** package manager (already installed if you're using this project)
   ```bash
   uv --version  # Should show v0.4.0 or higher
   ```

2. **Create PyPI account:**
   - Register at https://pypi.org/account/register/
   - Create API token at https://pypi.org/manage/account/token/

3. **Set PyPI token as environment variable:**
   ```bash
   # Add to ~/.zshrc or ~/.bashrc
   export UV_PUBLISH_TOKEN="pypi-AgEIcHlwaS5vcmcC..."  # Your API token

   # Or set for current session only
   export UV_PUBLISH_TOKEN="your-token-here"
   ```

   Alternative: Use `--token` flag with each publish command

## Publishing Steps

### 1. Update Version
Edit `pyproject.toml` and bump version number:
```toml
version = "1.0.1"  # Update this
```

### 2. Build Distribution
```bash
# Clean previous builds
rm -rf dist/ build/ *.egg-info

# Build package
uv build
```

This creates:
- `dist/vector-memory-mcp-1.0.0.tar.gz` (source distribution)
- `dist/vector_memory_mcp-1.0.0-py3-none-any.whl` (wheel)

### 3. Test with TestPyPI (Optional but Recommended)

```bash
# Upload to TestPyPI using uv
uv publish --publish-url https://test.pypi.org/legacy/ --token your-testpypi-token

# Or with environment variable
export UV_PUBLISH_TOKEN="your-testpypi-token"
uv publish --publish-url https://test.pypi.org/legacy/

# Test installation
uvx --index-url https://test.pypi.org/simple/ vector-memory-mcp --working-dir .
```

### 4. Upload to PyPI

```bash
# Upload to production PyPI using uv
uv publish --token your-pypi-token

# Or with environment variable (recommended)
export UV_PUBLISH_TOKEN="your-pypi-token"
uv publish

# Alternative: specify token inline
uv publish --token pypi-AgEIcHlwaS5vcmcC...
```

### 5. Verify Installation

```bash
# Test installation from PyPI
uvx vector-memory-mcp --working-dir /path/to/project

# Check package on PyPI
open https://pypi.org/project/vector-memory-mcp/
```

## Usage After Publishing

### Direct Execution (like npx)
```bash
# Run without installation
uvx vector-memory-mcp --working-dir /path/to/project
```

### Claude Desktop Configuration
```json
{
  "mcpServers": {
    "vector-memory": {
      "command": "uvx",
      "args": [
        "vector-memory-mcp",
        "--working-dir",
        "/absolute/path/to/your/project"
      ]
    }
  }
}
```

### Alternative: pipx Installation
```bash
# Install globally
pipx install vector-memory-mcp

# Run
vector-memory-mcp --working-dir /path/to/project
```

## Automation with GitHub Actions

Create `.github/workflows/publish.yml`:

```yaml
name: Publish to PyPI

on:
  release:
    types: [published]

jobs:
  publish:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Install uv
        uses: astral-sh/setup-uv@v3
        with:
          version: "latest"

      - name: Set up Python
        uses: actions/setup-python@v5
        with:
          python-version: '3.11'

      - name: Build package
        run: uv build

      - name: Publish to PyPI
        env:
          UV_PUBLISH_TOKEN: ${{ secrets.PYPI_API_TOKEN }}
        run: uv publish
```

**Setup GitHub Secret:**
1. Go to your repo → Settings → Secrets and variables → Actions
2. Create new secret: `PYPI_API_TOKEN`
3. Paste your PyPI API token

## Important Notes

⚠️ **SQLite Extensions Requirement:**
- Standard Python does NOT support SQLite loadable extensions
- Users need Python compiled with `--enable-loadable-sqlite-extensions`
- Recommend conda/miniforge Python or custom-compiled Python

📝 **Version Management:**
- Follow semantic versioning (MAJOR.MINOR.PATCH)
- Update CHANGELOG.md for each release
- Tag releases in git: `git tag v1.0.0 && git push --tags`

🔐 **Security:**
- Never commit PyPI tokens to git
- Use GitHub Secrets for CI/CD
- Regularly rotate API tokens
