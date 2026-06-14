# DockCheck – Document Compliance Analysis

A Laravel application for tracking documents, defining compliance rules, and automatically analysing documents against those rules using OpenAI.

## Features

- **Document Management** – Upload PDF, DOCX, TXT, and images; view file metadata and status.
- **Rule Management** – Create and edit compliance rules (requirements/ criteria).
- **AI-Powered Analysis** – Extracts document content, summarises it, and cross-references against active rules to detect inconsistencies.
- **Audit Trail** – Every uploaded document, analysis run, and rule change is logged.
- **Blade UI** – Full web interface for all features.
- **REST API** – All core endpoints available under `/api/`.

## Requirements

- PHP 8.2+
- SQLite (default), MySQL, or PostgreSQL
- OpenAI API key

## Installation

1. **Clone the project**

```bash
git clone <repository-url> dockcheck
cd dockcheck
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Set up environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure OpenAI** – Edit `.env`:

```dotenv
OPENAI_API_KEY=sk-your-key-here
OPENAI_ORGANIZATION=org-xxxxx       # optional
OPENAI_MODEL=gpt-4o-mini            # or gpt-4.1, gpt-4-turbo, etc.
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Install & build frontend assets**

```bash
npm install && npm run build
```

7. **Start the dev server**

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser. Register an account and log in.

## Usage

### 1. Create Rules

Navigate to **Rules** → **+ New Rule** and define compliance criteria. For example:

> "All contracts must include a termination clause with a minimum 30-day notice period."

### 2. Upload a Document

Go to **Documents**, choose a file (PDF, DOCX, TXT) and click **Upload**.

### 3. Run Analysis

On the document detail page, click **Run Analysis**. The app will:
1. Extract text from the document.
2. Send the text + active rules to OpenAI.
3. Parse the response and display:
   - Summary
   - Key points
   - Inconsistencies with severity levels (low / medium / high)

### 4. Review Audit Logs

Visit **Audit Logs** to see a filtered record of every action.

## API Endpoints

All API routes are prefixed with `/api` and require **authentication via Sanctum** (token or session).

| Method | Endpoint                        | Description               |
|--------|--------------------------------|---------------------------|
| POST   | `/api/documents`                | Upload a document         |
| GET    | `/api/documents`                | List documents            |
| GET    | `/api/documents/{id}`           | Document detail + analysis|
| POST   | `/api/documents/{id}/analyze`   | Trigger analysis          |
| POST   | `/api/rules`                    | Create a rule             |
| GET    | `/api/rules`                    | List rules                |
| PUT    | `/api/rules/{id}`               | Update a rule             |
| GET    | `/api/audit-logs`               | List audit logs           |

## OpenAI Prompt Structure

The analysis prompt (built in `app/Services/DocumentAnalysisService.php`) works as follows:

1. The extracted document text is placed inside a `""" ... """` block.
2. All active rules are listed below with their ID, title, and description.
3. The model is instructed to return a **JSON object** with:
   - `summary` – short 2–3 sentence summary
   - `key_points` – array of important points
   - `inconsistencies` – array of `{ rule_title, description, severity }` objects
4. The response is parsed, and results are stored in the `document_analyses` table.

## Inconsistency Detection

Inconsistencies are detected by comparing the document content against each active rule via the LLM. The service:
- Sends both document text and rules in a single prompt.
- Asks the model to identify conflicts, missing requirements, or potential non-compliances.
- Maps returned rule titles back to database rule IDs.
- Assigns a severity level: **low** (minor concern), **medium** (moderate), **high** (critical failure).

## Audit Logging

Every significant event writes to the `audit_logs` table via `App\Services\AuditService`:

- `document_uploaded` – payload: filename, size, mime type
- `analysis_started` / `analysis_completed` – payload: summary length, counts
- `analysis_failed` – payload: error message
- `rule_created` / `rule_updated` – payload: title

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DocumentController.php
│   │   ├── RuleController.php
│   │   └── AuditLogController.php
│   └── Requests/
│       ├── StoreDocumentRequest.php
│       ├── StoreRuleRequest.php
│       └── UpdateRuleRequest.php
├── Models/
│   ├── Document.php
│   ├── Rule.php
│   ├── DocumentAnalysis.php
│   └── AuditLog.php
└── Services/
    ├── AuditService.php
    └── DocumentAnalysisService.php

database/migrations/
├── 2025_01_01_000001_create_documents_table.php
├── 2025_01_01_000002_create_rules_table.php
├── 2025_01_01_000003_create_document_analyses_table.php
└── 2025_01_01_000004_create_audit_logs_table.php

resources/views/
├── layouts/app.blade.php
├── documents/index.blade.php, show.blade.php
├── rules/index.blade.php, create.blade.php, edit.blade.php
├── audit-logs/index.blade.php
└── dashboard.blade.php

routes/
├── web.php      – Web routes (auth required)
└── api.php      – API routes (auth:sanctum required)
```

## Testing

```bash
php artisan test
```

## License

MIT
