# Vector Memory MCP Server

A **secure, vector-based memory server** for Claude Desktop using `sqlite-vec` and `sentence-transformers`. This MCP server provides persistent semantic memory capabilities that enhance AI coding assistants by remembering and retrieving relevant coding experiences, solutions, and knowledge.

## ✨ Features

- **🔍 Semantic Search**: Vector-based similarity search using 384-dimensional embeddings
- **🏷️ Semantic Normalization**: Auto-merge similar tags, normalize categories, structured colon tags
- **📊 IDF Tag Weights**: Frequency-based weighting for improved search relevance
- **💾 Persistent Storage**: SQLite database with vector indexing via `sqlite-vec`
- **🔒 Security First**: Input validation, path sanitization, and resource limits
- **⚡ High Performance**: Fast embedding generation with `sentence-transformers`
- **🧹 Auto-Cleanup**: Intelligent memory management and cleanup tools
- **📈 Rich Statistics**: Comprehensive memory database analytics
- **🔄 Automatic Deduplication**: SHA-256 content hashing prevents storing duplicate memories
- **🧠 Smart Cleanup Algorithm**: Prioritizes memory retention based on recency, access patterns, and importance

## 🛠️ Technical Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Vector DB** | sqlite-vec | Vector storage and similarity search |
| **Embeddings** | sentence-transformers/all-MiniLM-L6-v2 | 384D text embeddings |
| **Normalization** | Semantic similarity + guards | Tag/category auto-merge |
| **MCP Framework** | FastMCP | High-level tools-only server |
| **Dependencies** | uv script headers | Self-contained deployment |
| **Security** | Custom validation | Path/input sanitization |
| **Testing** | pytest + coverage | Comprehensive test suite |

## 📁 Project Structure

```
vector-memory-mcp/
├── main.py                              # Main MCP server entry point
├── README.md                            # This documentation
├── requirements.txt                     # Python dependencies
├── pyproject.toml                       # Modern Python project config
├── .python-version                      # Python version specification
├── claude-desktop-config.example.json  # Claude Desktop config example
│
├── src/                                # Core package modules
│   ├── __init__.py                    # Package initialization
│   ├── models.py                      # Data models & configuration
│   ├── security.py                    # Security validation & sanitization
│   ├── embeddings.py                  # Sentence-transformers wrapper
│   ├── memory_store.py                # SQLite-vec operations
│   ├── README_AGENTS.md               # Agent documentation (4 levels)
│   └── CASES_AGENTS.md                # Use cases for Brain ecosystem
│
└── .gitignore                         # Git exclusions
```

## 🗂️ Organization Guide

This project is organized for clarity and ease of use:

- **`main.py`** - Start here! Main server entry point
- **`src/`** - Core implementation (security, embeddings, memory store)
- **`claude-desktop-config.example.json`** - Configuration template

**New here?** Start with `main.py` and `claude-desktop-config.example.json`

## 🚀 Quick Start

### Prerequisites

- Python 3.10 or higher (recommended: 3.11)
- [uv](https://docs.astral.sh/uv/) package manager
- Claude Desktop app

**Installing uv** (if not already installed):

macOS and Linux:
```bash
curl -LsSf https://astral.sh/uv/install.sh | sh
```

Verify installation:
```bash
uv --version
```

### Installation

#### Option 1: Quick Install via uvx (Recommended)

The easiest way to use this MCP server - no cloning or setup required!

**Once published to PyPI**, you can use it directly:

```bash
# Run without installation (like npx)
uvx vector-memory-mcp --working-dir /path/to/your/project
```

**Claude Desktop Configuration** (using uvx):
```json
{
  "mcpServers": {
    "vector-memory": {
      "command": "uvx",
      "args": [
        "vector-memory-mcp",
        "--working-dir",
        "/absolute/path/to/your/project",
        "--memory-limit",
        "100000"
      ]
    }
  }
}
```

> **Note:** `--memory-limit` is optional. Omit it to use default 10,000 entries.

> **Note**: Publishing to PyPI is in progress. See [PUBLISHING.md](PUBLISHING.md) for details.

#### Option 2: Install from Source (For Development)

1. **Clone the project**:
   ```bash
   git clone <repository-url>
   cd vector-memory-mcp
   ```

2. **Install dependencies** (automatic with uv):
   Dependencies are automatically managed via inline metadata in main.py. No manual installation needed.

   To verify dependencies:
   ```bash
   uv pip list
   ```

3. **Test the server**:
   ```bash
   # Test with sample working directory
   uv run main.py --working-dir ./test-memory
   ```

4. **Configure Claude Desktop**:

   Copy the example configuration:
   ```bash
   cp claude-desktop-config.example.json ~/path/to/your/config/
   ```

   Open Claude Desktop Settings → Developer → Edit Config, and add (replace paths with absolute paths):

   ```json
   {
     "mcpServers": {
       "vector-memory": {
         "command": "uv",
         "args": [
           "run",
           "/absolute/path/to/vector-memory-mcp/main.py",
           "--working-dir",
           "/your/project/path",
           "--memory-limit",
           "100000"
         ]
       }
     }
   }
   ```

   Important:
   - Use absolute paths, not relative paths
   - `--memory-limit` is optional (default: 10,000)
   - For large projects, use 100,000-1,000,000

5. **Restart Claude Desktop** and look for the MCP integration icon.

#### Option 3: Install with pipx (Alternative)

```bash
# Install globally (once published to PyPI)
pipx install vector-memory-mcp

# Run
vector-memory-mcp --working-dir /path/to/your/project
```

**Claude Desktop Configuration** (using pipx):
```json
{
  "mcpServers": {
    "vector-memory": {
      "command": "vector-memory-mcp",
      "args": [
        "--working-dir",
        "/absolute/path/to/your/project",
        "--memory-limit",
        "100000"
      ]
    }
  }
}
```

## 📚 Usage Guide

### Available Tools

#### 1. `store_memory` - Store Knowledge
Store coding experiences, solutions, and insights:

```
Please store this memory:
Content: "Fixed React useEffect infinite loop by adding dependency array with [userId, apiKey]. The issue was that the effect was recreating the API call function on every render."
Category: bug-fix
Tags: ["react", "useEffect", "infinite-loop", "hooks"]
```

#### 2. `search_memories` - Semantic Search
Find relevant memories using natural language:

```
Search for: "React hook dependency issues"
```

#### 3. `list_recent_memories` - Browse Recent
See what you've stored recently:

```
Show me my 10 most recent memories
```

#### 4. `get_memory_stats` - Database Health
View memory database statistics:

```
Show memory database statistics
```

#### 5. `clear_old_memories` - Cleanup
Clean up old, unused memories:

```
Clear memories older than 30 days, keep max 1000 total
```

#### 6. `get_by_memory_id` - Retrieve Specific Memory
Get full details of a specific memory by its ID:

```
Get memory with ID 123
```

Returns all fields including content, category, tags, timestamps, access count, and metadata.

#### 7. `delete_by_memory_id` - Delete Memory
Permanently remove a specific memory from the database:

```
Delete memory with ID 123
```

Removes the memory from both metadata and vector tables atomically.

#### 8. `get_unique_tags` - List All Tags
Get all unique tags currently used in memories:

```
Show all unique tags
```

Returns sorted list of tags from memory metadata.

#### 9. `get_canonical_tags` - List Canonical Tags
Get all canonical (normalized) tags:

```
Show canonical tags
```

Returns the normalized tag forms after semantic merging. Useful for understanding tag consolidation.

#### 10. `get_tag_frequencies` - Tag Usage Statistics
Get frequency count for all canonical tags:

```
Show tag frequencies
```

Shows how often each tag is used. Higher frequency = more common tag.

#### 11. `get_tag_weights` - IDF Weights
Get IDF-based weights for search relevance:

```
Show tag weights
```

Returns weights calculated as `1 / log(1 + frequency)`:
- Common tags (api, auth) → lower weight (less discriminative)
- Rare tags (module:terminal) → higher weight (more discriminative)

#### 12. `cookbook` - Knowledge Base (CRITICAL)

**CRITICAL: READ THIS FIRST before using any other tools. Without this, you are operating blind.**

```
# FIRST: Initialize context (READ THIS FIRST)
mcp__vector-memory__cookbook()

# List available categories with keys
mcp__vector-memory__cookbook(include="categories")

# Cases by key (exact match)
mcp__vector-memory__cookbook(include="cases", case_category="gates-rules")
mcp__vector-memory__cookbook(include="cases", case_category="search")

# Search in cookbook
mcp__vector-memory__cookbook(include="cases", query="JWT token")
mcp__vector-memory__cookbook(include="docs", query="tag normalization", level=2)

# Pagination
mcp__vector-memory__cookbook(include="cases", query="task", limit=5, offset=0)

# Documentation by level
mcp__vector-memory__cookbook(include="docs", level=0)  # Quick start
mcp__vector-memory__cookbook(include="docs", level=2)  # Advanced patterns

# Full debug info
mcp__vector-memory__cookbook(include="all", level=3)
```

**Parameters:**
| Parameter | Values | Description |
|-----------|--------|-------------|
| `include` | "init", "docs", "cases", "categories", "all" | What to return (default "init") |
| `level` | 0-3 | Docs verbosity (default 0) |
| `case_category` | string | Filter cases by key (exact) or title (partial) |
| `query` | string | Text search in content |
| `limit` | 1-50 | Max results (default 10) |
| `offset` | int | Pagination offset (default 0) |

**Include Modes:**
| Mode | Returns |
|------|---------|
| `init` | FIRST READ - quick start + available resources |
| `docs` | Documentation by level |
| `cases` | Use case scenarios (filtered by category/query) |
| `categories` | List of categories with keys and descriptions |
| `all` | Everything combined |

**Docs Levels:**
| Level | Content |
|-------|---------|
| 0 | Identity & Quick Start |
| 1 | Practical Usage |
| 2 | Advanced Patterns |
| 3 | Architecture & Internals |

**Category Keys:**
| Key | Description |
|-----|-------------|
| `cookbook-usage` | How to use cookbook() tool |
| `store` | Store memories with deduplication |
| `search` | Multi-probe search, pre-task mining |
| `statistics` | Memory stats, tag frequencies |
| `task-management` | Memory integration with Task MCP |
| `brain-docs` | CLI docs indexing |
| `agent-coordination` | Brain delegation, multi-agent |
| `integration` | Multi-source knowledge, error recovery |
| `debugging` | Debug flow with memory capture |
| `cleanup` | Delete operations, cleanup by age |
| `gates-rules` | CRITICAL/HIGH priority rules |
| `task-integration` | Memory-Task workflow patterns |

**Case Categories:** Cookbook Usage, Store, Search, Statistics, Task Creation, Task Decomposition, Task Status, Brain Docs, Agent Coordination, Integration, Debugging, Cleanup

**Contains:** 4 documentation levels + 12 use case categories + Brain ecosystem reference.

### Memory Categories

| Category | Use Cases |
|----------|-----------|
| `code-solution` | Working code snippets, implementations |
| `bug-fix` | Bug fixes and debugging approaches |
| `architecture` | System design decisions and patterns |
| `learning` | New concepts, tutorials, insights |
| `tool-usage` | Tool configurations, CLI commands |
| `debugging` | Debugging techniques and discoveries |
| `performance` | Optimization strategies and results |
| `security` | Security considerations and fixes |
| `other` | Everything else |

## 🏷️ Semantic Normalization

The server automatically normalizes tags and categories using semantic similarity to maintain consistency.

### Tag Normalization

When storing memories, similar tags are merged into **canonical tags**:

| Input Tags | Canonical Result |
|------------|------------------|
| `api v2.0`, `api 2`, `API version 2` | `api v2.0` |
| `php8`, `PHP 8`, `php-8` | `php8` |
| `laravel`, `laravel framework` | `laravel` (with substring boost) |

### Merge Rules

**✅ Merges when:**
- Same version: `api v2.0` ↔ `api 2` (threshold 0.85)
- High similarity: `php8` ↔ `php 8` (threshold 0.90)
- Substring boost: `laravel` ⊂ `laravel framework` (+0.03 similarity)

**❌ Never merges:**
- Different versions: `api v1` ≠ `api v2`
- Different numbers: `php7` ≠ `php8`
- Structured vs plain: `type:refactor` ≠ `refactor`
- Same prefix, different suffix: `type:refactor` ≠ `type:bug`
- Stop-words: `api` ≠ `rest api`, `ui` ≠ `web ui`

### Structured Tags (Colon Tags)

Use structured tags for fine-grained organization:

```
["type:refactor", "priority:high", "domain:api", "module:auth"]
```

**Allowed prefixes:** `type`, `domain`, `strict`, `cognitive`, `batch`, `module`, `vendor`, `priority`, `scope`, `layer`

Invalid prefixes are rejected: `random:stuff` → removed

### Category Normalization

Categories are also normalized semantically. Short inputs use dictionary fallback:

| Input | Output |
|-------|--------|
| `bugfix`, `bug`, `fix` | `bug-fix` |
| `auth`, `sec` | `security` |
| `perf`, `opt` | `performance` |
| `debug` | `debugging` |
| `arch`, `design` | `architecture` |

### Thresholds

| Threshold | Value | Purpose |
|-----------|-------|---------|
| Tag merge | 0.90 | Default similarity for merge |
| Same version | 0.85 | Lower threshold for same-version tags |
| Substring boost | +0.03 | Boost for subset tags |
| Category | 0.50 | Category matching threshold |
| Min substring length | 4 | Minimum for substring boost |

### Stop-Words (No Substring Boost)

These tags never get substring boost (too generic):

```
api, ui, db, test, auth, infra, ci, cd, app, lib, sdk, cli, gui, web, sql, orm, log, cfg, env, dev, prod, stg
```

### Tag Hygiene Guidelines

**Good tags** (describe subject/domain):
```
["authentication", "laravel", "middleware", "api v2"]
```

**Bad tags** (describe tools/activities):
```
["phpstan", "ci", "tests", "run-migration"]  # Don't use these
```

### IDF Tag Weights

Tags are weighted using IDF (Inverse Document Frequency):

```
weight = 1 / log(1 + frequency)
```

| Tag | Frequency | Weight | Interpretation |
|-----|-----------|--------|----------------|
| `api` | 50 | 0.26 | Very common, low discriminative power |
| `laravel` | 10 | 0.43 | Common, moderate discriminative power |
| `module:terminal` | 2 | 1.44 | Rare, high discriminative power |

Use `get_tag_weights` to see all weights. Rare tags boost search relevance more than common tags.

## 🔧 Configuration

### Command Line Arguments

The server supports the following arguments:

```bash
# Run with uv (recommended) - default 10,000 memory limit
uv run main.py --working-dir /path/to/project

# With custom memory limit for large projects
uv run main.py --working-dir /path/to/project --memory-limit 100000

# Working directory is where memory database will be stored
uv run main.py --working-dir ~/projects/my-project --memory-limit 500000
```

**Available Options:**
- `--working-dir` (required): Directory where memory database will be stored
- `--memory-limit` (optional): Maximum number of memory entries
  - Default: 10,000 entries
  - Minimum: 1,000 entries
  - Maximum: 10,000,000 entries
  - Recommended for large projects: 100,000-1,000,000

### Working Directory Structure

```
your-project/
├── memory/
│   └── vector_memory.db    # SQLite database with vectors
├── src/                    # Your project files
└── other-files...
```

### Security Limits

- **Max memory content**: 10,000 characters
- **Max total memories**: Configurable via `--memory-limit` (default: 10,000 entries)
- **Max search results**: 50 per query
- **Max tags per memory**: 10 tags
- **Path validation**: Blocks suspicious characters

## 🎯 Use Cases

### For Individual Developers

```
# Store a useful code pattern
"Implemented JWT refresh token logic using axios interceptors"

# Store a debugging discovery  
"Memory leak in React was caused by missing cleanup in useEffect"

# Store architecture decisions
"Chose Redux Toolkit over Context API for complex state management because..."
```

### For Team Workflows

```
# Store team conventions
"Team coding style: always use async/await instead of .then() chains"

# Store deployment procedures
"Production deployment requires running migration scripts before code deploy"

# Store infrastructure knowledge
"AWS RDS connection pooling settings for high-traffic applications"
```

### For Learning & Growth

```
# Store learning insights
"Understanding JavaScript closures: inner functions have access to outer scope"

# Store performance discoveries
"Using React.memo reduced re-renders by 60% in the dashboard component"

# Store security learnings
"OWASP Top 10: Always sanitize user input to prevent XSS attacks"
```

## 🔍 How Semantic Search Works

The server uses **sentence-transformers** to convert your memories into 384-dimensional vectors that capture semantic meaning:

### Example Searches

| Query | Finds Memories About |
|-------|---------------------|
| "authentication patterns" | JWT, OAuth, login systems, session management |
| "database performance" | SQL optimization, indexing, query tuning, caching |
| "React state management" | useState, Redux, Context API, state patterns |
| "API error handling" | HTTP status codes, retry logic, error responses |

### Similarity Scoring

- **0.9+ similarity**: Extremely relevant, almost exact matches
- **0.8-0.9**: Highly relevant, strong semantic similarity  
- **0.7-0.8**: Moderately relevant, good contextual match
- **0.6-0.7**: Somewhat relevant, might be useful
- **<0.6**: Low relevance, probably not helpful

## 📊 Database Statistics

The `get_memory_stats` tool provides comprehensive insights:

```json
{
  "total_memories": 247,
  "memory_limit": 100000,
  "usage_percentage": 0.25,
  "categories": {
    "code-solution": 89,
    "bug-fix": 67,
    "learning": 45,
    "architecture": 23,
    "debugging": 18,
    "other": 5
  },
  "recent_week_count": 12,
  "database_size_mb": 15.7,
  "health_status": "Healthy"
}
```

### Statistics Fields Explained

- **total_memories**: Current number of memories stored in the database
- **memory_limit**: Maximum allowed memories (configurable via --memory-limit, default: 10,000)
- **usage_percentage**: Database capacity usage (total_memories / memory_limit * 100)
- **categories**: Breakdown of memory count by category type
- **recent_week_count**: Number of memories created in the last 7 days
- **database_size_mb**: Physical size of the SQLite database file on disk
- **health_status**: Overall database health indicator based on usage and performance metrics

## 🛡️ Security Features

### Input Validation
- Sanitizes all user input to prevent injection attacks
- Removes control characters and null bytes
- Enforces length limits on all content

### Path Security
- Validates and normalizes all file paths
- Prevents directory traversal attacks
- Blocks suspicious character patterns

### Resource Limits
- Limits total memory count and individual memory size
- Prevents database bloat and memory exhaustion
- Implements cleanup mechanisms for old data

### SQL Safety
- Uses parameterized queries exclusively
- No dynamic SQL construction from user input
- SQLite WAL mode for safe concurrent access

## 🔧 Troubleshooting

### Common Issues

#### Server Not Starting
```bash
# Check if uv is installed
uv --version

# Test server manually
uv run main.py --working-dir ./test

# Check Python version
python --version  # Should be 3.10+
```

#### Claude Desktop Not Connecting
1. Verify absolute paths in configuration
2. Check Claude Desktop logs: `~/Library/Logs/Claude/`
3. Restart Claude Desktop after config changes
4. Test server manually before configuring Claude

#### Memory Search Not Working
- Verify sentence-transformers model downloaded successfully
- Check database file permissions in memory/ directory
- Try broader search terms
- Review memory content for relevance

#### Performance Issues
- Run `get_memory_stats` to check database health
- Use `clear_old_memories` to clean up old entries
- Consider increasing hardware resources for embedding generation

### Debug Mode

Run the server manually to see detailed logs:

```bash
uv run main.py --working-dir ./debug-test
```

## 🚀 Advanced Usage

### Batch Memory Storage

Store multiple related memories by calling the tool multiple times through Claude Desktop interface.

### Memory Organization Strategies

#### By Project
Use tags to organize by project:
- `["project-alpha", "frontend", "react"]`
- `["project-beta", "backend", "node"]`
- `["project-gamma", "devops", "docker"]`

#### By Technology Stack
- `["javascript", "react", "hooks"]`
- `["python", "django", "orm"]`
- `["aws", "lambda", "serverless"]`

#### By Problem Domain
- `["authentication", "security", "jwt"]`
- `["performance", "optimization", "caching"]`
- `["testing", "unit-tests", "mocking"]`

### Integration with Development Workflow

#### Code Review Learnings
```
"Code review insight: Extract validation logic into separate functions for better testability and reusability"
```

#### Sprint Retrospectives
```
"Sprint retrospective: Using feature flags reduced deployment risk and enabled faster rollbacks"
```

#### Technical Debt Tracking
```
"Technical debt: UserService class has grown too large, needs refactoring into smaller domain-specific services"
```

## 📈 Performance Benchmarks

Based on testing with various dataset sizes:

| Memory Count | Search Time | Storage Size | RAM Usage |
|--------------|-------------|--------------|-----------|
| 1,000 | <50ms | ~5MB | ~100MB |
| 5,000 | <100ms | ~20MB | ~200MB |
| 10,000 | <200ms | ~40MB | ~300MB |

*Tested on MacBook Air M1 with sentence-transformers/all-MiniLM-L6-v2*

## 🔧 Advanced Implementation Details

### Database Indexes

The memory store uses 4 optimized indexes for performance:

1. **idx_category**: Speeds up category-based filtering and statistics
2. **idx_created_at**: Optimizes temporal queries and recent memory retrieval
3. **idx_content_hash**: Enables fast deduplication checks via SHA-256 hash lookups
4. **idx_access_count**: Improves cleanup algorithm efficiency by tracking usage patterns

### Deduplication System

Content deduplication uses SHA-256 hashing to prevent storing identical memories:
- Hash calculated on normalized content (trimmed, lowercased)
- Check performed before insertion
- Duplicate attempts return existing memory ID
- Reduces storage overhead and maintains data quality

### Access Tracking

Each memory tracks usage statistics for intelligent management:
- **access_count**: Number of times memory retrieved via search or direct access
- **last_accessed_at**: Timestamp of most recent access
- **created_at**: Original creation timestamp
- Used by cleanup algorithm to identify valuable vs. stale memories

### Cleanup Algorithm

Smart cleanup prioritizes memory retention based on multiple factors:
1. **Recency**: Newer memories are prioritized over older ones
2. **Access patterns**: Frequently accessed memories are protected
3. **Age threshold**: Configurable days_old parameter for hard cutoff
4. **Count limit**: Maintains max_memories cap by removing least valuable entries
5. **Scoring system**: Combines access_count and recency for retention decisions

## 🤝 Contributing

This is a standalone MCP server designed for personal/team use. For improvements:

1. **Fork** the repository
2. **Modify** as needed for your use case
3. **Test** thoroughly with your specific requirements
4. **Share** improvements via pull requests

## 📄 License

This project is released under the MIT License.

## 🙏 Acknowledgments

- **sqlite-vec**: Alex Garcia's excellent SQLite vector extension
- **sentence-transformers**: Nils Reimers' semantic embedding library  
- **FastMCP**: Anthropic's high-level MCP framework
- **Claude Desktop**: For providing the MCP integration platform

---

**Built for developers who want persistent AI memory without the complexity of dedicated vector databases.**
