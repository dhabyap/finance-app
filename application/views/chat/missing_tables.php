<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">AI CHAT - SETUP REQUIRED</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-sm border-brutal bg-white font-mono fw-bold">BACK</a>
    </div>

    <div class="card card-brutal p-4 bg-white">
        <div class="alert alert-danger border-brutal mb-4">
            <div class="font-mono fw-bold">Database tables are missing</div>
            <div class="font-mono small">
                Missing: <b><?= htmlspecialchars(implode(', ', $missing ?? [])) ?></b>
            </div>
        </div>

        <div class="font-mono mb-3">
            Your database was created before the AI Chat feature. Please apply the SQL patch below.
        </div>

        <div class="font-mono small mb-2"><b>Option A (recommended):</b> import `sql/ai_chat_tables.sql` into your `finance_app` database.</div>
        <div class="font-mono small mb-3"><b>Option B:</b> copy/paste the SQL below into your MySQL client.</div>

        <pre class="border-brutal p-3 bg-light pre-wrap-scrollable"><code>CREATE TABLE IF NOT EXISTS `chat_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `chat_threads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `role` enum('user','assistant','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_json` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `chat_threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</code></pre>

        <div class="font-mono small mt-3">
            After applying the patch, refresh this page.
        </div>
    </div>
</div>
