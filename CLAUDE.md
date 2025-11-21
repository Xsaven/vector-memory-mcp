# Vector Memory MCP Server

## Project Overview
MCP (Model Context Protocol) Server для векторної пам'яті з використанням sqlite-vec для семантичного пошуку.

## Technology Stack
- **Python**: 3.11.8 (requires >= 3.10)
- **Package Manager**: `uv` (сучасний Python package manager)
- **Database**: SQLite 3.43.2 + sqlite-vec extension
- **Embeddings**: sentence-transformers/all-MiniLM-L6-v2 (384-dimensional vectors)
- **MCP Framework**: FastMCP >= 0.3.0

## Key Dependencies
- `mcp>=0.3.0` - Model Context Protocol framework
- `sqlite-vec>=0.1.6` - Vector search extension для SQLite
- `sentence-transformers>=2.2.2` - Embedding models

## Project Structure
- `main.py` - Entry point with uv script configuration
- `requirements.txt` - Python dependencies for pip/venv compatibility
- `pyproject.toml` - Modern Python project configuration
- `.python-version` - Python version specification (3.11)
- `claude-desktop-config.example.json` - Claude Desktop configuration template
- `src/models.py` - Data models and configuration
- `src/security.py` - Security validation and sanitization
- `src/memory_store.py` - Vector memory storage operations
- `src/embeddings.py` - Embedding generation
- `memory/` - SQLite database storage directory

## How to Run

### Standalone
```bash
# On Apple Silicon (M1/M2/M3) - use run-arm64.sh script
./run-arm64.sh --working-dir /your/working/directory

# With custom memory limit (default: 10,000 entries)
./run-arm64.sh --working-dir /your/working/directory --memory-limit 100000

# Alternative with conda Python (has SQLite extensions support)
~/miniconda3/envs/vector-mcp/bin/python main.py --working-dir ./ --memory-limit 100000

# Using uv (requires Python with SQLite extensions support)
uv run main.py --working-dir ./ --memory-limit 100000
```

### Configuration Options

- `--working-dir` - Working directory for memory database (required, default: current directory)
- `--memory-limit` - Maximum number of memory entries (optional, default: 10,000)
  - Minimum: 1,000 entries
  - Maximum: 10,000,000 entries
  - Recommended for large projects: 100,000-1,000,000

**⚠️ IMPORTANT for macOS Users:**
- Standard Python from python.org does NOT support SQLite loadable extensions
- Use conda/miniforge Python or compile Python with `--enable-loadable-sqlite-extensions`
- On Apple Silicon, ensure you're running native arm64 Python, not x86_64 through Rosetta

### Claude Desktop Integration
Використовуй `claude-desktop-config.example.json` як шаблон.

Конфігурація для Claude Desktop:
```json
{
  "mcpServers": {
    "vector-memory": {
      "command": "/absolute/path/to/run-arm64.sh",
      "args": [
        "--working-dir",
        "/your/project/path",
        "--memory-limit",
        "100000"
      ]
    }
  }
}
```

**ВАЖЛИВО:**
- Використовуй абсолютні шляхи, не відносні!
- `--memory-limit` опціональний, за замовчуванням 10,000
- Для великих проектів рекомендовано 100,000-1,000,000

## Database Architecture
- `memory_metadata` - Метадані спогадів (content, category, tags, timestamps)
- `memory_vectors` - Векторна таблиця (vec0 virtual table)
- Індекси на category, created_at, content_hash, access_count

## Important Notes
- **sqlite-vec** працює як extension для SQLite, завантажується через `sqlite_vec.load(conn)`
- **uv** використовується замість venv - він керує ізольованим оточенням автоматично
- Векторний пошук використовує 384-вимірні embeddings
- База даних: `./memory/vector_memory.db`

## Security Features
- Working directory validation
- Input sanitization
- Content hash для дедуплікації
- Resource limits для захисту від DoS

## Development Notes
- Проект налаштований як uv script з inline metadata (/// script ///)
- Не потрібно створювати venv вручну
- Всі залежності автоматично керуються через uv