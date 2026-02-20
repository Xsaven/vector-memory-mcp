"""
Test CASES_AGENTS.md integrity: all categories must have description and content.

Run: pytest tests/test_cases_integrity.py -v

This test ensures:
1. All categories have non-empty description in <!-- description: ... -->
2. All categories have actual content (not empty when queried)
3. Category naming follows conventions
4. Gates & Rules has 6 constitutional gates
"""

import re
import pytest
from pathlib import Path


CASES_FILE = Path(__file__).parent.parent / "src" / "CASES_AGENTS.md"


def parse_categories_from_cases():
    """Parse all category headers with their descriptions from CASES_AGENTS.md"""
    content = CASES_FILE.read_text()
    
    pattern = r'^## (.+?)(?: Scenarios)?(?: \([A-Z]+\))?\n(?:<!-- description: (.+?) -->)?'
    matches = re.findall(pattern, content, re.MULTILINE)
    
    categories = []
    for name, description in matches:
        key = name.lower()
        key = re.sub(r'[&():]', '', key)
        key = re.sub(r'\s+', '-', key)
        key = re.sub(r'-+', '-', key).strip('-')
        
        categories.append({
            "name": name,
            "key": key,
            "description": description.strip() if description else ""
        })
    
    return categories


def get_expected_categories():
    """Expected categories based on cookbook API"""
    return [
        "cookbook-usage",
        "store",
        "search",
        "statistics",
        "task-management",
        "brain-docs",
        "agent-coordination",
        "integration",
        "debugging",
        "cleanup",
        "gates-rules",
        "task-integration",
        "technical-debt-integration",
        "reference-brain-ecosystem-tags",
        "constitutional-learn-protocol",
        "self-improvement",
        "lightweight-lawyer-gate",
        "standard-search-patterns-reference",
        "tag-taxonomy-reference",
        "non-goals-reference",
        "workflow",
        "governance",
    ]


class TestCasesIntegrity:
    """Test suite for CASES_AGENTS.md integrity"""
    
    def test_cases_file_exists(self):
        """CASES_AGENTS.md must exist"""
        assert CASES_FILE.exists(), f"CASES_AGENTS.md not found at {CASES_FILE}"
    
    def test_all_categories_have_description(self):
        """All categories must have non-empty description"""
        categories = parse_categories_from_cases()
        
        missing_descriptions = []
        for cat in categories:
            if not cat["description"]:
                missing_descriptions.append(cat["key"])
        
        assert not missing_descriptions, (
            f"Categories missing description:\n"
            + "\n".join(f"  - {k}" for k in missing_descriptions)
            + "\n\nFix: Add <!-- description: Your description here --> after the ## header"
        )
    
    def test_expected_categories_exist(self):
        """All expected categories must be present in CASES_AGENTS.md"""
        categories = parse_categories_from_cases()
        found_keys = {cat["key"] for cat in categories}
        expected_keys = set(get_expected_categories())
        
        missing = expected_keys - found_keys
        extra = found_keys - expected_keys
        
        error_parts = []
        if missing:
            error_parts.append(f"Missing categories:\n" + "\n".join(f"  - {k}" for k in sorted(missing)))
        if extra:
            error_parts.append(f"Unexpected categories:\n" + "\n".join(f"  - {k}" for k in sorted(extra)))
        
        assert not missing and not extra, "\n\n".join(error_parts)
    
    def test_category_key_format(self):
        """Category keys must follow lowercase-with-dashes format"""
        categories = parse_categories_from_cases()
        
        invalid_keys = []
        for cat in categories:
            if not re.match(r'^[a-z0-9]+(-[a-z0-9]+)*$', cat["key"]):
                invalid_keys.append(f"{cat['name']} -> {cat['key']}")
        
        assert not invalid_keys, (
            f"Invalid category key formats:\n"
            + "\n".join(f"  - {k}" for k in invalid_keys)
            + "\n\nKeys must be lowercase with single dashes: 'gates-rules', not 'Gates & Rules'"
        )
    
    def test_description_quality(self):
        """Descriptions should be meaningful (not too short, not placeholder)"""
        categories = parse_categories_from_cases()
        
        weak_descriptions = []
        for cat in categories:
            desc = cat["description"]
            if desc.lower() in ["", "todo", "tbd", "n/a", "none"]:
                weak_descriptions.append(f"{cat['key']}: '{desc}' (empty/placeholder)")
            elif len(desc) < 20:
                weak_descriptions.append(f"{cat['key']}: '{desc}' (too short, min 20 chars)")
        
        assert not weak_descriptions, (
            f"Weak descriptions found:\n"
            + "\n".join(f"  - {d}" for d in weak_descriptions)
            + "\n\nDescriptions should be informative and at least 20 characters"
        )
    
    def test_gates_rules_has_content(self):
        """gates-rules category must have 6 constitutional gates"""
        content = CASES_FILE.read_text()
        
        gates_match = re.search(
            r'^## Gates.*?Rules.*?(?=^## |\Z)',
            content,
            re.MULTILINE | re.DOTALL
        )
        
        assert gates_match, "gates-rules section not found in CASES_AGENTS.md"
        
        gates_content = gates_match.group(0)
        
        required_gates = [
            "Gate 1: MCP-JSON-ONLY",
            "Gate 2: Lightweight Lawyer Gate",
            "Gate 3: Constitutional Learn Protocol",
            "Gate 4: Category Discipline Contract",
            "Gate 5: Cookbook-First Gate",
            "Gate 6: Failure Escalation Gate"
        ]
        
        missing_gates = []
        for gate in required_gates:
            if gate.lower() not in gates_content.lower():
                missing_gates.append(gate)
        
        assert not missing_gates, (
            f"gates-rules missing required gates:\n"
            + "\n".join(f"  - {g}" for g in missing_gates)
        )
        
        assert "[CRITICAL]" in gates_content, "gates-rules must contain CRITICAL priority rules"
        assert "[HIGH]" in gates_content, "gates-rules must contain HIGH priority rules"

    def test_no_banned_phrases_in_new_categories(self):
        """New categories (workflow, governance) must not contain uncertainty triggers"""
        content = CASES_FILE.read_text()

        # Extract only new categories: workflow, governance
        new_sections = []
        for section_name in ["Workflow", "Governance"]:
            match = re.search(
                rf'^## {section_name}.*?(?=^## |\Z)',
                content,
                re.MULTILINE | re.DOTALL
            )
            if match:
                new_sections.append((section_name, match.group(0)))

        banned_patterns = [
            (r'[Tt]rigger.*[Uu]ncertainty', 'Trigger...Uncertainty'),
            (r'[Ww]hen uncertain', 'when uncertain'),
            (r'[Bb]efore assuming', 'before assuming'),
        ]

        violations = []
        for section_name, section_content in new_sections:
            for pattern, label in banned_patterns:
                matches = re.findall(pattern, section_content)
                if matches:
                    violations.append(f"{section_name}: found '{label}' ({len(matches)}x)")

        assert not violations, (
            f"Banned phrases in new categories:\n"
            + "\n".join(f"  - {v}" for v in violations)
            + "\n\nThese phrases are uncertainty triggers banned by Brain governance."
            + "\n  Use compile-time presets and explicit onViolation instead."
        )

    def test_mcp_examples_json_syntax(self):
        """MCP call examples in new categories must use JSON object syntax"""
        content = CASES_FILE.read_text()

        # Extract workflow and governance sections
        new_sections = []
        for section_name in ["Workflow", "Governance"]:
            match = re.search(
                rf'^## {section_name}.*?(?=^## |\Z)',
                content,
                re.MULTILINE | re.DOTALL
            )
            if match:
                new_sections.append((section_name, match.group(0)))

        violations = []
        for section_name, section_content in new_sections:
            # Find all mcp__ calls
            mcp_calls = re.findall(
                r'(mcp__[a-z_-]+__[a-z_]+)\(([^)]*)\)',
                section_content,
                re.DOTALL
            )
            for tool_name, payload in mcp_calls:
                payload_stripped = payload.strip()
                if not payload_stripped:
                    continue  # Empty call (e.g., cookbook()) is valid
                # Payload must start with { (JSON object)
                if not payload_stripped.startswith('{'):
                    violations.append(
                        f"{section_name}: {tool_name}() payload not JSON object: "
                        f"'{payload_stripped[:50]}...'"
                    )

        assert not violations, (
            f"MCP examples must use JSON object syntax:\n"
            + "\n".join(f"  - {v}" for v in violations)
            + "\n\nCorrect: mcp__tool__op({\"key\": \"value\"})"
            + "\n  Wrong: mcp__tool__op(key=\"value\")"
        )

    def test_workflow_has_verification_step(self):
        """Workflow category must contain verification/confirm pattern"""
        content = CASES_FILE.read_text()

        workflow_match = re.search(
            r'^## Workflow.*?(?=^## |\Z)',
            content,
            re.MULTILINE | re.DOTALL
        )

        assert workflow_match, "Workflow section not found in CASES_AGENTS.md"

        workflow_content = workflow_match.group(0)

        assert re.search(r'[Vv]erif', workflow_content), (
            "Workflow section must contain verification step (verify/verified/verification)"
        )
        assert re.search(r'[Cc]onfirm', workflow_content), (
            "Workflow section must contain confirmation step (confirm/confirmed)"
        )

    def test_governance_has_iron_rule_reference(self):
        """Governance category must reference iron rules"""
        content = CASES_FILE.read_text()

        governance_match = re.search(
            r'^## Governance.*?(?=^## |\Z)',
            content,
            re.MULTILINE | re.DOTALL
        )

        assert governance_match, "Governance section not found in CASES_AGENTS.md"

        governance_content = governance_match.group(0)

        assert "iron rule" in governance_content.lower(), (
            "Governance section must reference iron rules"
        )
        assert "compile-time" in governance_content.lower(), (
            "Governance section must reference compile-time constraints"
        )


if __name__ == "__main__":
    print("=== CASES_AGENTS.md Integrity Check ===\n")
    
    categories = parse_categories_from_cases()
    expected = set(get_expected_categories())
    
    print("Categories with descriptions:\n")
    for cat in categories:
        status = "✓" if cat["description"] else "✗ MISSING"
        print(f"  {status} {cat['key']}")
        if cat["description"]:
            print(f"      {cat['description'][:60]}{'...' if len(cat['description']) > 60 else ''}")
    
    print(f"\nTotal: {len(categories)} categories")
    print(f"Missing descriptions: {sum(1 for c in categories if not c['description'])}")
    
    found_keys = {cat["key"] for cat in categories}
    missing = expected - found_keys
    if missing:
        print(f"\nMissing categories: {missing}")
