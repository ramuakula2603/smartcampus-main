Project overview
- This repository is a CodeIgniter (MVC) PHP application (front controller: `index.php`).
- Key folders: `application/controllers` (controllers), `application/models`, `application/views` (UI/templates), `application/libraries` and `application/third_party` (external libs), `uploads/` (media).

Where to start (big picture)
- Web UI served by Apache/XAMPP (open `http://localhost/amt` in dev). The app uses CI controllers under `application/controllers/admin` for admin features.
- Chat UI: `application/views/user/chat/index.php` (client-side JS handles send/receive). Server endpoints used by chat are in `application/controllers/admin/Chat.php` and the custom `Generatepaper` controller.
- Generate paper flow: controller `application/controllers/admin/Generatepaper.php` implements `save_message`, `upload_file`, and `generate_pdf` endpoints. PDF template: `application/views/admin/generatepaper/question_paper_pdf.php`.

Critical integration points and patterns
- PDF library: controller loads `m_pdf` via `$this->load->library('m_pdf')`. Verify the library exists under `application/libraries` or `application/third_party` / composer `vendor` directory. PDF templates are plain views that receive prepared `$data` and are rendered to HTML then written to PDF.
- Media and logos: college logo is expected in `uploads/school_content/logo/` and the project uses a `media_storage` helper/library to build URLs. Reuse this path when adding header/footer branding.
- Chat persistence: messages are saved in `chat_messages_gp` via `Chatmessage_model` (`application/models/Chatmessage_model.php`). New AI replies are stored with `save_message()`.

Repository-specific conventions
- Controller classes live under `application/controllers/*` and admin controllers are namespaced by folder `admin` (e.g. `admin/Generatepaper.php`). Views follow the `application/views/<folder>/<name>.php` convention and are loaded with `$this->load->view(..., $data, true)` to capture HTML.
- AJAX endpoints often return JSON; some endpoints expect `embed=true` in POST to return JSON with `base64` PDF data for embedding in the chat window.
- Reuse existing templates: look at `Tcgeneration.php` and `Halltickectgeneration.php` for header/footer and media patterns when designing new PDFs (they already include logo/header handling).

How to implement chat → PDF (practical recipe)
1. Front-end (chat view): allow selecting messages. Edit `application/views/user/chat/index.php` and:
   - Add checkboxes or selection UI next to each message `<li>` (AI replies from DB are printed in `.messages ul`).
   - Add a `Generate PDF` button in the chat controls.
2. Client-side (example): collect selected messages and POST to the controller endpoint. Example AJAX (use existing `baseurl` global):
   ```js
   const messages = [...document.querySelectorAll('.messages li.selected p')].map(p => p.innerHTML);
   $.post(baseurl + 'admin/Generatepaper/generate_pdf', { messages: messages, embed: 'true' }, function(resp){
     if (resp.status === 'success' && resp.pdf_data) {
       const byteChars = atob(resp.pdf_data);
       const byteNumbers = new Array(byteChars.length).fill(0).map((_,i)=>byteChars.charCodeAt(i));
       const byteArray = new Uint8Array(byteNumbers);
       const blob = new Blob([byteArray], {type: 'application/pdf'});
       const url = URL.createObjectURL(blob);
       window.open(url, '_blank'); // or embed inside chat as <iframe>
     } else {
       alert('PDF generation failed: ' + (resp.message||'unknown'))
     }
   }, 'json');
   ```
3. Server-side: `Generatepaper::generate_pdf()` already accepts `messages` POST, renders `application/views/admin/generatepaper/question_paper_pdf.php`, and uses `m_pdf` to produce a PDF. It supports `embed=true` to return a JSON payload with `pdf_data` (base64).

Files to inspect / modify for this task
- Front-end chat UI: `application/views/user/chat/index.php` (JS handlers) — add selection + button here.
- Controller: `application/controllers/admin/Generatepaper.php` — endpoints `save_message`, `upload_file`, `generate_pdf` (already contains the generation flow and robust error handling).
- Model: `application/models/Chatmessage_model.php` (save/get messages table `chat_messages_gp`).
- Template: `application/views/admin/generatepaper/question_paper_pdf.php` — modify/extend layout, header, CSS or branding.
- Existing PDF examples: `application/controllers/admin/Tcgeneration.php`, `application/controllers/admin/Halltickectgeneration.php` and their views for header/footer usage.

Local dev and troubleshooting checklist
- Run with XAMPP/Apache and point browser at `http://localhost/amt`.
- Ensure `uploads/` and `uploads/school_content/logo/` are writable by the web server.
- Verify `m_pdf` (or equivalent mpdf TCPDF library) is available; if missing, look under `application/third_party` or `vendor` and run `composer install` if project uses composer.
- Use browser DevTools Network to inspect AJAX payloads to `admin/Generatepaper/generate_pdf` and the JSON response (`pdf_data` base64).
- Check PHP error logs and `application/logs` when PDF generation fails. The controller logs useful debug info with `error_log()`.

Examples of exact endpoints you will use
- POST `admin/Generatepaper/save_message` — forwards user message to n8n and saves reply.
- POST `admin/Generatepaper/generate_pdf` — inputs: `messages[]` (or single `messages`), optional `embed=true`. Returns base64 PDF when `embed=true`.

If anything is unclear or you want me to implement the front-end selection + AJAX integration now, tell me and I will patch `application/views/user/chat/index.php` and wire the button (I can also add a small client-side preview embed). Please indicate whether to open the PDF in a new tab or embed it directly inside the chat window.
