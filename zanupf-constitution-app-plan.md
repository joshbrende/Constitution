## ZANU–PF Constitution Mobile App – Execution Plan

### 1. Objectives & Rationale

- **Primary goal**: Make the ZANU–PF constitution accessible, understandable, and engaging for Zimbabwean youths (and the broader membership) via a modern mobile application.
- **Key problems addressed**:
  - **Size & complexity** of the constitution text (difficult to navigate and digest).
  - **Low engagement** with party mandates among youths despite high smartphone penetration.
  - **Lack of structured dialogue** channels between youths and the party.
- **High‑level outcomes**:
  - Youths can **search, browse, and summarise** constitutional content easily (including in offline mode).
  - The party gains **data‑driven insight** into what topics youths are reading and asking about.
  - A foundation for **credible and constructive dialogue** between citizens and the party.

---

### 2. Technology Stack

- **Mobile App**
  - **Framework**: React Native with **Expo** (managed workflow).
  - **Platforms**: Android (priority), iOS (where feasible).
  - **Language**: TypeScript.
  - **State Management**: Redux Toolkit or Zustand.
  - **Navigation**: React Navigation.
  - **Offline Storage**: `expo-sqlite` or `realm` (for structured content), `AsyncStorage` for lightweight preferences.
  - **Internationalisation (optional later)**: `react-intl` or `i18next` for multi-language tooltips/explanations (e.g. English + Shona + Ndebele summaries).

- **Backend**
  - **Framework**: Laravel (latest LTS).
  - **Language**: PHP 8.x.
  - **Database**: MySQL / MariaDB (or PostgreSQL if closer to existing government stack).
  - **API Style**: RESTful JSON API; optional GraphQL gateway later.
  - **Auth**: Laravel Sanctum or Passport (tokens for mobile clients).
  - **Caching**: Redis for hot content (popular articles/sections).
  - **Queue**: Laravel Queues (Redis or database driver) for background jobs (analytics processing, notifications).

- **Infrastructure & Integration**
  - **Hosting**: Government / party data centre or trusted cloud (e.g. private VPS) behind **VPN / secure network** for sensitive admin tools.
  - **API Gateway / Reverse Proxy**: Nginx or Apache.
  - **Monitoring & Logs**: Laravel Telescope for dev, centralised logs (e.g. ELK/Graylog) for production.
  - **Government system integration**: via **secure REST APIs** or message queues; endpoints and IP ranges to be aligned with government ICT.

---

### 3. Core Features (MVP → Phase 2)

- **MVP (Phase 1)**
  - **Constitution browser**:
    - Hierarchical navigation (Parts → Chapters → Sections → Articles).
    - Per-section detail view with clear headings, summaries, and annotations.
  - **Powerful search**:
    - Full-text search across all sections.
    - Filters (e.g. by chapter, topic, keywords like “youth”, “elections”, “rights”, “duties”).
  - **Plain‑language summaries**:
    - Youth‑friendly explanation beneath each section.
    - Visual indicators of difficulty level (e.g. “basic”, “intermediate”, “advanced”).
  - **Offline access**:
    - Initial constitution content bundled with the app, stored locally.
    - Background sync when online to fetch updates / new summaries.
  - **Bookmarks & notes**:
    - Save favourite sections.
    - Add private notes per section (stored locally, synced to backend when logged in).
  - **Feedback & questions**:
    - Simple “Was this clear?” yes/no feedback.
    - Question submission (optional login) to party’s information team.

- **Phase 2+**
  - **Discussion & polls**:
    - Topic-based polls and surveys for youths on key issues.
    - Comment threads moderated by party youth league / information department.
  - **Push notifications**:
    - Updates on amendments.
    - Invitations to policy discussions and events.
  - **Gamification**:
    - Badges for reading key sections, completing “learning paths” (e.g. “Know Your Rights”).
  - **AI-powered assistance (optional)**:
    - Ask questions in natural language and get constitution-based answers.
    - Strict grounding in constitutional text to avoid misinformation.

---

### 4. UI/UX Design (ZANU–PF Brand)

- **Colour Palette** (approximate)
  - **Primary**: Deep Green `#006400` (backgrounds, headers).
  - **Secondary**: Strong Red `#B22222` (accent, buttons).
  - **Highlight**: Gold/Yellow `#FFD700` (icons, highlights).
  - **Neutral**: White `#FFFFFF`, Light Gray `#F5F5F5`, Dark Gray `#333333` for text.

- **Design Principles**
  - **Clarity over decoration**: Political colours used for structure (headers, accents) but content areas kept mostly **white / light** for readability.
  - **High contrast**: Ensure WCAG‑compliant contrast between text and background.
  - **Minimal cognitive load**:
    - Short paragraphs, bullet points, and collapsible explanations.
    - “Read more” pattern to progressively reveal complexity.

- **Key Screens**
  - **Onboarding**
    - 2–3 slides explaining purpose: “Know Your Constitution”, “Shape Your Future”, “Engage Constructively”.
    - Option to choose **language** and **text size**.
  - **Home**
    - Top area: quick entry points (e.g. “Browse Constitution”, “Search”, “Youth Rights”, “Elections”).
    - Highlight banners for current campaigns or amendments.
  - **Constitution Browser**
    - Left/right swiping between sections, or list drill‑down.
    - Clear hierarchy breadcrumbs (e.g. “Chapter 2 > Youth Rights > Section 12”).
  - **Section Detail**
    - Top: official section title and reference number.
    - Middle: original legal text (scrollable).
    - Below: simplified summary, bullet key points.
    - Actions: bookmark, add note, share, feedback.
  - **Search**
    - Prominent search field.
    - Results grouped by chapter with snippet preview.
  - **Profile (optional but recommended)**
    - Basic identity (name/alias, age range, province).
    - Settings: language, theme (light/dark), font size, download-for-offline toggles.

- **Typography & Hierarchy**
  - **Typefaces**
    - Primary: A clean, geometric or humanist sans-serif (e.g. **Inter**, **Roboto**, **SF Pro** on iOS).
  - **Hierarchy**
    - **H1** (screen title): 24–28 pt, bold, green or dark gray.
    - **H2** (chapter title): 20–22 pt, semi-bold.
    - **H3** (section heading): 18 pt, semi-bold.
    - **Body – legal text**: 14–16 pt, regular, line-height 1.5+, dark gray on white.
    - **Body – summaries/notes**: 14 pt, regular, slightly lighter color.
  - **Accessibility**
    - Dynamic font scaling support.
    - Clear tap targets (44x44 pt minimum).

---

### 5. Expo Go – Installation & Execution

- **Prerequisites**
  - Node.js (LTS), Yarn or npm installed on dev machine.
  - Git for version control.

- **Setup Steps**
  - **1. Create the Expo app**
    - `npx create-expo-app zanupf-constitution-app --template tabs (or blank)`
  - **2. Install Expo Go on device**
    - From the Google Play Store or Apple App Store, search for `Expo Go` and install.
  - **3. Run development server**
    - In project directory: `npx expo start` (or `yarn expo start`).
    - Scan the QR code using Expo Go (or use LAN / Tunnel depending on network).
  - **4. Development workflow**
    - Enable **fast refresh** for instant UI updates.
    - Use device logs & React Native Debugger/Flipper for debugging.
  - **5. Building standalone apps**
    - Use EAS (Expo Application Services) for building production APK/AAB/IPA when ready.

---

### 6. Backend – Laravel Architecture & Roles

- **High-Level Architecture**
  - **Layers**:
    - Controllers: handle HTTP requests and responses.
    - Services: business logic (e.g. content versioning, sync rules).
    - Repositories: database access.
    - Jobs/Listeners: async processing (analytics, email, notifications).
  - **APIs**
    - Public, read‑only endpoints for constitution content and summaries (with versioning).
    - Authenticated endpoints for bookmarks, notes, feedback, polls, and admin.

- **Roles & Permissions**
  - **Guest**
    - Read-only access to constitution, summaries, and non-sensitive educational content.
    - Limited feedback (e.g. thumbs up/down; optional contact info).
  - **Registered Youth / Citizen**
    - All guest permissions.
    - Create/manage bookmarks and notes (synced).
    - Participate in polls and surveys.
    - Optional participation in moderated discussion forums.
  - **Content Editor (Party Information Dept.)**
    - Manage constitutional content structure (chapters, sections, tags).
    - Write and update summaries and annotations.
    - Schedule content releases/updates.
  - **Moderator (Youth League / Communications)**
    - Moderate comments, Q&A, and discussion threads.
    - Manage polls and surveys.
  - **Administrator**
    - Manage users and roles.
    - Configure app‑wide settings (e.g. feature flags, maintenance mode).
    - Review analytics reports (usage, popular sections, feedback stats).

- **Security & Government Integration**
  - Use **JWT / Sanctum tokens** for mobile clients.
  - IP whitelisting, VPN and/or private network peering between app servers and government networks.
  - Audit logging for admin actions.
  - Rate limiting at API gateway level.

---

### 7. Offline-First & Government Network Connectivity

- **Offline-First Strategy**
  - Bundle initial constitution data with the app installation.
  - On first launch with internet, check for data updates (versioned JSON delivered from Laravel backend).
  - Store content locally via SQLite/Realm, with migrations when versions change.
  - Allow full browsing, search, bookmarks, and notes **without connectivity**.
  - Queue user actions (notes, feedback, polls) and **sync** when connectivity resumes.

- **Sync Architecture**
  - Lightweight `/sync` endpoint:
    - Client sends last synced timestamp + local content version.
    - Server returns:
      - New or updated sections/summaries.
      - Pending server-side updates (e.g. poll results, messages).
  - Conflict resolution:
    - For bookmarks/notes: “last writer wins” + audit trail, or simple merge per user.

- **Government Network Connectivity**
  - Design APIs and DNS so that:
    - The app can reach servers both over **public internet** and **secure government network** where required.
    - Admin tools for moderators and content editors are hosted behind government firewalls / VPN.
  - Where appropriate, integrate with:
    - Existing **single sign-on** (SSO) or identity providers used by government.
    - Official communication channels (e.g. SMS gateways, email infrastructure) for notifications.

---

### 8. Execution Roadmap

- **Phase 0 – Inception (1–2 weeks)**
  - Finalise requirements with ZANU–PF leadership, Youth League, and ICT.
  - Obtain official, up‑to‑date `constitution-of-zanupf.pdf` and any amendments.
  - Decide hosting environment and integration constraints with government ICT.

- **Phase 1 – Architecture & Design (2–3 weeks)**
  - Detailed UX flows and high‑fidelity designs in party colours.
  - Technical architecture diagrams (mobile, API, data model, integrations).
  - Define content model for constitution (chapters, sections, tags, topics, keywords).

- **Phase 2 – Backend & Content Pipeline (3–4 weeks)**
  - Set up Laravel project, roles, and permissions.
  - Implement core read APIs (`/chapters`, `/sections`, `/search`).
  - Build admin panel for content editors to maintain summaries and annotations.
  - Import and structure the constitution text from the PDF into database.

- **Phase 3 – Mobile MVP (4–6 weeks)**
  - Implement navigation, constitution browser, search, bookmarks, notes, and basic feedback.
  - Implement offline storage with initial sync.
  - Integrate with Laravel backend for content updates and user data.

- **Phase 4 – Pilot & Feedback (3–4 weeks)**
  - Closed pilot with selected youth groups across provinces.
  - Gather analytics and qualitative feedback.
  - Refine UI/UX, fix bugs, and adjust content clarity.

- **Phase 5 – Public Rollout & Phase 2 Features**
  - Launch on Google Play Store (and other distribution channels as appropriate).
  - Add advanced features (polls, discussions, notifications, AI assistant where acceptable).
  - Establish ongoing content governance and technical maintenance processes.

---

### 9. Next Steps

- Confirm this execution plan aligns with party leadership expectations.
- Decide on **hosting environment** and **integration points** with existing government networks.
- Begin detailed **data modelling** of the constitution PDF and define how sections will be structured inside the database and the app.
-
---

### 10. ZANU PF Academy – Learning Management System (LMS)

- **Official Name & Purpose**
  - **Name**: `ZANU PF Academy`.
  - **Purpose**: Provide a structured **constitutional and ideological learning environment** where all members (especially youths) complete formal **learning pathways** with **assessments** (multiple‑choice and other formats) to demonstrate understanding of the ZANU–PF constitution, structures, and values.

- **Core LMS Concepts & Modularisation**
  - **Courses**: e.g. “ZANU–PF Constitution Basics”, “Advanced Cadre Training”.
  - **Modules**: Thematic units inside a course (e.g. “Youth Rights”, “Party Structures”, “Elections and Procedures”).
  - **Lessons**: Individual learning units with explanations, examples, and references to specific constitutional sections.
  - **Assessments** (academic terminology instead of “quiz”):
    - Formative and summative **knowledge assessments** with multiple‑choice items.
    - Linked to lessons/sections to verify conceptual understanding.
  - **Question / Item Bank**:
    - Central repository of MCQs and other item types, each mapped to sections, difficulty levels, and learning outcomes.
  - **User Progress & Certification**:
    - Track enrollment, completion, assessment attempts, results, and certification for members.

- **User Roles & Access Control**
  - **Student (Member / Youth)**
    - Access to **Student Dashboard** in the Academy.
    - Enrolled in mandatory and optional courses.
    - Access lessons, attempt assessments, view results, and monitor progress.
    - Manage profile, settings, and help resources.
  - **Instructor**
    - Access to **Instructor Dashboard** after explicit **Admin approval**.
    - Create and maintain courses, modules, lessons, and assessment items.
    - View progress and performance of students enrolled in their courses.
    - No system‑wide administrative privileges.
  - **Administrator**
    - Approves or rejects **Instructor applications**.
    - Manages users and roles, including bulk enrollment into courses (e.g. by province or structure).
    - Configures which courses and assessments are **mandatory** for specific roles or organisational structures.
    - Views Academy‑wide analytics and compliance reports.

- **Registration & Approval Flows**
  - **Student Registration**
    - Registration form for members/youths with the following required fields and controls:
      - **Name** (first name).
      - **Surname**.
      - **Email address** (used as login identifier).
      - **Password**.
      - **Retype Password** (with “eye” icon to toggle visibility for both password fields).
      - **Checkbox**: “I accept all terms and conditions” (must be checked to proceed; links to full terms and privacy policy).
      - **Sign Up** button.
    - On successful registration:
      - Create the user account with default **Student/Member** role.
      - Optionally capture additional profile fields (province, organisational structure such as Youth League/Women’s League/Main Wing) either on the same form or via a short follow‑up profile completion step.
    - On approval (automatic or manual), user receives **Student** role and gains access to ZANU PF Academy – Student Dashboard.
  - **Instructor Registration**
    - Dedicated **Instructor application** form:
      - Personal details, organisational role/position, area of expertise, motivation statement.
    - Status workflow: `pending` → `approved` → `active`.
    - Only when **approved by an Administrator** does the user gain the **Instructor** role and its privileges.
    - Admin may scope instructors (e.g. national vs provincial level).

- **Dashboards & Frontend Structure (Web – Livewire)**
  - **Shared Layout**
    - **Sidebar (left)** with ZANU PF Academy branding:
      - Deep green background (`#006400`) with gold accents (`#FFD700`) and minimal red highlights (`#B22222`) for active elements.
      - Navigation links with icons: Dashboard, Courses, Assessments, Profile, Settings, Help.
    - **Top bar**:
      - User avatar/name, notifications icon, quick access to help.
    - **Main content area**:
      - White/light background, card‑based sections, clear headings and typography.
  - **Student Dashboard**
    - Sidebar sections:
      - **Dashboard**: overview of learning status.
      - **My Learning Path**: mandatory and optional courses, each with progress bars.
      - **Assessments & Results**: list of completed and upcoming assessments, with marks and statuses.
      - **Certificates / Achievements** (future expansion).
      - **Profile**, **Settings**, **Help**.
    - Main widgets:
      - “Continue learning” (next pending lesson).
      - Status of **mandatory Academy courses** (e.g. “Constitution Basics – 60% complete”).
      - Recent assessment results and recommended next steps.
  - **Instructor Dashboard**
    - Sidebar sections:
      - **Dashboard**: teaching overview.
      - **My Courses**: create/edit courses and manage modules.
      - **Modules & Lessons**: list and structure teaching content.
      - **Assessment Items**: manage the question/item bank.
      - **Participants & Results**: view learner performance in their courses.
      - **Profile**, **Settings**, **Help**.
    - Main widgets:
      - Active courses and enrollment counts.
      - Recently updated lessons and assessments.
      - Performance indicators (e.g. overall pass rates, common misconceptions).

- **Data Model (High-Level)**
  - **`courses`**
    - `id`, `title`, `description`, `level` (e.g. basic/advanced), `is_mandatory`, `status`.
  - **`modules`**
    - `id`, `course_id`, `title`, `description`, `order`, `section_id` (optional link to a constitution section).
  - **`lessons`**
    - `id`, `module_id`, `title`, `content` (HTML/Markdown), `order`.
  - **`assessments`**
    - `id`, `course_id` or `module_id`, `title`, `num_questions`, `time_limit`, `minimum_competency_threshold` (pass mark), `max_attempts`, `assessment_type` (formative/summative).
  - **`questions`**
    - `id`, `section_id` (optional link to a constitutional section), `lesson_id` (optional), `question_text`, `explanation`, `difficulty` (`easy`, `medium`, `hard`), `status`.
  - **`question_options`**
    - `id`, `question_id`, `option_text`, `is_correct`.
  - **`assessment_questions`**
    - `assessment_id`, `question_id`, `order` (or configuration for randomisation).
  - **`assessment_attempts`**
    - `id`, `assessment_id`, `user_id`, `score`, `passed`, `started_at`, `completed_at`.
  - **`assessment_attempt_answers`**
    - `attempt_id`, `question_id`, `selected_option_id`, `is_correct`.
  - **`enrollments`**
    - `course_id`, `user_id`, `status` (`enrolled`, `in_progress`, `completed`, `failed`).

- **Mandatory Learning & Compliance**
  - Mark certain courses as **mandatory** for specific roles or organisational structures (e.g. all Youth League members must complete “ZANU–PF Constitution Basics”).
  - On user creation or role assignment, automatically:
    - Create appropriate `enrollments` records for required courses.
  - Use the Academy dashboards and reports to track:
    - Completion rates per course, province, structure, and age group.
  - Integrate with mobile notifications:
    - Reminders for incomplete mandatory assessments.

- **Integration with Constitution App & Opinion Dialogue**
  - **Constitution linkages**:
    - Modules and lessons reference specific constitutional sections and use the same section IDs as the main app.
    - Learners can jump from a lesson directly to the detailed section view (including amendments and summaries).
  - **Opinion dialogue integration (Livewire + API)**:
    - Attach discussion threads to advanced courses/modules so that members can engage in structured, moderated dialogue around case studies and constitutional scenarios.
  - **Mobile integration (Expo app)**:
    - Add a `ZANU PF Academy` tab exposing a simplified Student Dashboard, course list, lessons, and assessments.
    - Support **offline learning** by syncing course content and assessment items locally, then syncing assessment attempts when connectivity returns.

- **Creative & Unique Design Notes**
  - Maintain a **consistent political identity** while prioritising readability:
    - Deep green sidebar with subtle gradients or textures inspired by party imagery.
    - Gold highlights for progress bars, achievement badges, and course completion indicators.
    - Red used sparingly for calls‑to‑action (e.g. “Start Assessment”, “Continue Learning”) and warnings.
  - Design profile, settings, and help pages to be:
    - Simple, guided, and accessible (clear labels, help tooltips).
    - Ready for multi‑language support and accessibility features (font scaling, high‑contrast theme).

- **Exact Academy Database Tables (for implementation)**
  - **`academy_instructor_applications`**
    - `id` (PK)
    - `user_id` (FK → users)
    - `full_name`, `email`, `phone`
    - `province`, `district`, `structure` (e.g. Youth League, Women’s League)
    - `current_role` (e.g. “District Youth Chair”)
    - `area_of_expertise` (text)
    - `motivation` (longText)
    - `status` (`pending`, `approved`, `rejected`)
    - `reviewed_by` (FK → users, nullable)
    - `review_notes` (nullable text)
    - `created_at`, `updated_at`
  - **`academy_instructors`**
    - `id` (PK)
    - `user_id` (FK → users)
    - `scope_level` (`national`, `provincial`, `district`)
    - `province_id` / `district_id` (nullable, if scoped)
    - `active` (bool)
    - `created_at`, `updated_at`
  - **`academy_courses`**
    - `id` (PK)
    - `code` (short unique identifier, e.g. `ZP-CONST-101`)
    - `title`
    - `slug`
    - `description` (longText)
    - `level` (`basic`, `intermediate`, `advanced`)
    - `is_mandatory` (bool)
    - `status` (`draft`, `published`, `archived`)
    - `created_by` (FK → users/instructors)
    - `created_at`, `updated_at`
  - **`academy_modules`**
    - `id` (PK)
    - `course_id` (FK → academy_courses)
    - `title`
    - `description` (nullable)
    - `order` (int)
    - `section_id` (FK → sections, nullable, for direct constitution linkage)
    - `created_at`, `updated_at`
  - **`academy_lessons`**
    - `id` (PK)
    - `module_id` (FK → academy_modules)
    - `title`
    - `slug`
    - `content` (longText: HTML/Markdown)
    - `order` (int)
    - `estimated_minutes` (nullable int)
    - `created_at`, `updated_at`
  - **`academy_assessments`**
    - `id` (PK)
    - `course_id` (FK → academy_courses, nullable)
    - `module_id` (FK → academy_modules, nullable)
    - `title`
    - `description` (nullable)
    - `assessment_type` (`formative`, `summative`)
    - `num_questions` (int)
    - `time_limit_minutes` (nullable int)
    - `minimum_competency_threshold` (int, percentage)
    - `max_attempts` (nullable int, null = unlimited)
    - `status` (`draft`, `published`, `archived`)
    - `created_by` (FK → users/instructors)
    - `created_at`, `updated_at`
  - **`academy_questions`**
    - `id` (PK)
    - `section_id` (FK → sections, nullable)
    - `lesson_id` (FK → academy_lessons, nullable)
    - `question_text` (longText)
    - `explanation` (longText, nullable; rationale for correct answer)
    - `difficulty` (`easy`, `medium`, `hard`)
    - `status` (`draft`, `review`, `approved`, `retired`)
    - `created_by` (FK → users/instructors)
    - `created_at`, `updated_at`
  - **`academy_question_options`**
    - `id` (PK)
    - `question_id` (FK → academy_questions)
    - `option_text` (longText)
    - `is_correct` (bool)
    - `order` (int)
    - `created_at`, `updated_at`
  - **`academy_assessment_questions`**
    - `assessment_id` (FK → academy_assessments)
    - `question_id` (FK → academy_questions)
    - `order` (int, nullable if randomised)
  - **`academy_enrollments`**
    - `id` (PK)
    - `course_id` (FK → academy_courses)
    - `user_id` (FK → users)
    - `status` (`enrolled`, `in_progress`, `completed`, `failed`, `withdrawn`)
    - `progress_percent` (int, 0–100)
    - `started_at` (nullable)
    - `completed_at` (nullable)
    - `created_at`, `updated_at`
  - **`academy_assessment_attempts`**
    - `id` (PK)
    - `assessment_id` (FK → academy_assessments)
    - `user_id` (FK → users)
    - `score` (decimal or int)
    - `passed` (bool)
    - `attempt_number` (int)
    - `started_at`
    - `completed_at` (nullable)
    - `metadata` (json, nullable: device info, offline/online flags)
    - `created_at`, `updated_at`
  - **`academy_assessment_attempt_answers`**
    - `id` (PK)
    - `attempt_id` (FK → academy_assessment_attempts)
    - `question_id` (FK → academy_questions)
    - `selected_option_id` (FK → academy_question_options)
    - `is_correct` (bool)
    - `created_at`, `updated_at`

- **Key Livewire Components for ZANU PF Academy**
  - **Registration & Approval**
    - `Academy\StudentRegistrationForm`
      - Public form for members/youths to register as Academy students.
      - Captures personal details, province, structure; creates user + enrollment records for mandatory courses.
    - `Academy\InstructorApplicationForm`
      - Public/secured form where existing users apply to become instructors.
      - Writes to `academy_instructor_applications`.
    - `Academy\Admin\InstructorApplicationReview`
      - Admin view listing `pending` instructor applications with filters (province, structure).
      - Approve/reject actions update status and, on approval, create `academy_instructors` record and assign Instructor role.
  - **Student‑Facing Components**
    - `Academy\Student\Dashboard`
      - Overview of enrollments, progress, and upcoming assessments.
      - Uses `academy_enrollments` and `academy_assessment_attempts`.
    - `Academy\Student\CourseList`
      - Lists mandatory and optional courses with progress bars.
    - `Academy\Student\CourseDetail`
      - Shows modules, lessons, and associated assessments for a chosen course.
    - `Academy\Student\LessonViewer`
      - Displays lesson content; links to related constitution sections.
    - `Academy\Student\AssessmentRunner`
      - Runs an assessment (one question per screen), records answers and attempts.
      - Supports resume/retake logic based on `academy_assessment_attempts`.
    - `Academy\Student\ResultsHistory`
      - Shows past assessment attempts, scores, and pass/fail status.
  - **Instructor‑Facing Components**
    - `Academy\Instructor\Dashboard`
      - High‑level view of the instructor’s courses, active students, and key performance indicators.
    - `Academy\Instructor\CourseManager`
      - Create/edit courses (`academy_courses`) and manage course metadata.
    - `Academy\Instructor\ModuleLessonManager`
      - Manage modules and lessons in a structured tree per course.
    - `Academy\Instructor\QuestionBankManager`
      - CRUD for `academy_questions` and `academy_question_options`, with filters by course/module/section/difficulty.
    - `Academy\Instructor\AssessmentDesigner`
      - Configure `academy_assessments`, select questions, and define thresholds and time limits.
    - `Academy\Instructor\ParticipantResults`
      - View learner performance and attempt details for the instructor’s courses/assessments.
  - **Admin‑Facing Components**
    - `Academy\Admin\CourseCatalogue`
      - View and manage all courses, mark mandatory courses, and archive or publish.
    - `Academy\Admin\GlobalAnalytics`
      - Aggregate Academy statistics: enrollments, completions, assessment performance by province/structure/age.
    - `Academy\Admin\UserEnrollmentManager`
      - Bulk enrollment tools (e.g. enroll all Youth League members in a specific province into a mandatory course).

---

### 11. Advanced Reading, Library & Quality Requirements

- **Engineering Quality & Clean Coding**
  - Aim for **bug‑free, maintainable, and efficient** code across backend and mobile:
    - Use clear modular boundaries (constitution core, opinion dialogue, Academy, digital library).
    - Apply clean coding practices (small focused functions, meaningful naming, limited side effects).
    - Enforce consistent styles (PSR‑12 for PHP; ESLint + Prettier for TypeScript/React Native).
    - Implement automated tests for critical features (reading, search, notes, sync, assessments).

- **Digital ZANU PF Library Management**
  - Provide a digital library for official ZANU–PF materials alongside the constitution:
    - Store party documents (policy texts, congress resolutions, speeches, pamphlets, ideological manuals).
    - Categorise by type, topic, audience, language, and date.
    - Control access (public vs member‑only vs leadership‑only) using existing role/permission infrastructure.
    - Allow offline download of selected documents in the mobile app, with sync for updates.

- **Text Settings & Typeface**
  - Offer rich text‑presentation controls:
    - Typeface choice (at least a primary sans‑serif and an optional serif reading font).
    - Adjustable font size, line height, and margins.
    - Theme selection (light, dark, and high‑contrast).
  - Persist preferences per user and apply them consistently to:
    - Constitution sections.
    - Library documents.
    - Academy lessons.

  - **Dark mode requirement**
    - In dark mode, **all main textual content** (headings, body text, labels, notes, dialogue, Academy content, and library text) must render in **white or near‑white** on dark backgrounds to maximise legibility.
    - Accent colours (green, red, gold) should be adjusted only for non‑text elements (icons, borders, highlights) so that text remains clearly readable in white.

- **Reading Modes: Scrolling vs Paginated**
  - Support two reading modes for long‑form content:
    - **Continuous scrolling**: standard vertical scroll.
    - **Paginated**: fixed “pages” with left/right swipe and page indicator.
  - Allow users to switch mode in `Text Settings`, remembering the choice per device and content type.

- **Global Search**
  - Implement search that spans:
    - Entire constitution (sections, summaries, tags).
    - Party library documents (titles, abstracts, body text).
    - Academy course/lesson titles and possibly key concepts.
  - Use indexed search for performance and return:
    - Ranked results.
    - Highlighted snippets.
    - Direct navigation links to the relevant section/document.

- **Annotations & Highlighting**
  - Enable users to personalise study:
    - Highlight arbitrary text in multiple colours with optional labels.
    - Add annotations/notes linked to specific ranges in a section or document.
    - Manage all annotations via a “My Annotations” view (filterable by source, chapter, tag).
  - Ensure annotations are available offline and synced securely when connectivity is available.

- **Dictionary & External Reference Lookup**
  - Provide contextual lookup tools:
    - Built‑in dictionary or legal/political glossary for key terms (offline where possible).
    - Optional online lookup (e.g. Wikipedia or curated reference sites) via in‑app browser.
  - Triggered by long‑press/selection gestures on words or phrases.

- **Translation Support**
  - Allow translation of selected passages into supported languages:
    - Integrate with translation services such as DeepL and Yandex (or equivalents) through backend proxies.
    - Prioritise English ↔ major local languages (e.g. Shona, Ndebele) as quality allows.
  - Make it clear within the UI that translations are **aids for understanding** and that the official constitutional text is the authoritative version.

- **Text‑to‑Speech (TTS)**
  - Integrate TTS to read constitution sections, library documents, and Academy lessons:
    - Use platform‑native TTS capabilities via Expo or native modules.
    - Provide basic controls (play/pause, speed, skip to next/previous paragraph).
  - Optionally highlight the sentence or paragraph being read to support comprehension and accessibility.

- **Cross‑Platform Synchronisation**
  - For authenticated users, synchronise:
    - Reading position (per section/document).
    - Notes, highlights, and bookmarks.
    - Academy progress and assessment history.
  - Handle conflicts gracefully (e.g. last‑writer‑wins with audit logs) and design sync endpoints to work well with intermittent connectivity.

- **Accessibility**
  - Design with accessibility as a core requirement:
    - Support screen readers and keyboard navigation (for web).
    - Provide scalable fonts and high‑contrast themes.
    - Ensure sufficient hit‑area sizes and avoid colour‑only indicators.
  - Periodically review against accessibility guidelines and incorporate feedback from real users with diverse needs.

- **Chapters, Summaries & Reviews**
  - Preserve clear hierarchical navigation:
    - Parts → Chapters → Sections → Sub‑sections, with visible breadcrumbs.
  - After major structural units (e.g. each chapter), provide:
    - Concise **chapter summaries** in plain language.
    - Optional **chapter reviews** in ZANU PF Academy (short assessments or reflection prompts) linked directly to the relevant reading.

---

### 12. Governance, Security & Operations

- **Security, Privacy & Risk Management**
  - Define a basic **threat model**:
    - Identify sensitive data (user profiles, dialogue content, assessment results, admin actions).
    - Document likely risks (account takeover, data leaks, misuse of dialogue platform).
  - Apply security controls:
    - Enforce HTTPS/TLS everywhere, strong password policies, and optional multi‑factor authentication for admins and instructors.
    - Use role‑based access control (RBAC) consistently across constitution, dialogue, Academy, and library modules.
    - Limit direct internet exposure of admin tools (VPN/IP‑restricted access within government/party networks).
  - Privacy and data minimisation:
    - Collect only necessary personal data (e.g. avoid storing highly sensitive information unless required).
    - Anonymise or aggregate data in analytics wherever possible.

- **Content Governance & Moderation Policy**
  - Establish written guidelines for:
    - How constitutional content and summaries are drafted, reviewed, and approved (legal + political oversight).
    - What is acceptable in opinion dialogue (prohibited content, tone, off‑topic posts).
    - How moderators should respond to misinformation, abuse, or sensitive issues.
  - Define a moderation process:
    - Timeframes for reviewing flagged content.
    - Escalation paths (to legal/communications leadership) for complex cases.
    - Optional appeal or review process for removed content.

- **Analytics, KPIs & Continuous Improvement**
  - Track key indicators:
    - App usage (DAU/MAU), most‑read sections, search terms.
    - Dialogue activity (threads, posts, sentiment indicators if used).
    - Academy participation (enrolments, completion rates, assessment performance) by province, age group, and structure.
  - Establish feedback loops:
    - Regular reports to party leadership and Youth League structures.
    - Use insights to refine learning content, clarify confusing sections, and guide communications campaigns.
  - Tooling:
    - Use privacy‑aware analytics tools and central dashboards (e.g. self‑hosted solutions where appropriate).

- **DevOps, Deployment & Maintenance**
  - Environments:
    - Separate **development**, **staging**, and **production** environments with controlled promotion of changes.
  - CI/CD:
    - Automated pipelines to run tests, static analysis, and security checks before deployment.
    - Versioning of backend APIs and mobile app releases, with clear release notes.
  - Backups & Disaster Recovery:
    - Regular automated database backups (including constitution data, dialogue, Academy records).
    - Tested restore procedures and defined Recovery Time Objective (RTO) / Recovery Point Objective (RPO).
  - Monitoring & Incident Response:
    - Application and infrastructure monitoring (uptime, performance, error rates).
    - Logging of key events (logins, admin actions, content changes).
    - Documented incident response plan (who is called, how issues are triaged and communicated).

- **Ownership & Organisational Roles**
  - Assign clear ownership:
    - **Product owner** (within party leadership/ICT) responsible for feature roadmap and prioritisation.
    - **Technical owner/team** responsible for codebase, infrastructure, and day‑to‑day operations.
    - **Content owners** (Information Dept., Youth League, legal advisors) for constitutional text, summaries, Academy content, and official documents.
  - Governance structure:
    - Periodic multi‑stakeholder review meetings (technical, legal, political, youth representatives) to:
      - Review analytics and user feedback.
      - Approve major feature changes and content updates.
      - Ensure alignment with party strategy and legal obligations.

---

### 13. Website & Communications Integration

- **Brand & Messaging Alignment**
  - Align the app’s visual identity, tone, and messaging with the official party website [`zanupf.org.zw`](https://www.zanupf.org.zw/):
    - Use consistent slogans and themes such as **“Unity, Peace and Development”**, **Vision 2030**, and “The People’s Party”.
    - Mirror key visual elements (logo usage, colour palette, typography hierarchy) while optimising for mobile UI/UX.
  - Ensure that references to the Presidium, party organs, and strategic programmes are up‑to‑date and consistent with the website.

- **Content Linkage Between App and Website**
  - From the app to the website:
    - Provide deep links from relevant app sections (e.g. news, projects, leadership) to corresponding pages on the official site (Presidium, Party Organs, News, Projects, Events).
    - Include a “More on the Web” / “Official Announcements” area that opens curated website content in an in‑app browser.
  - From the website to the app:
    - Add clear calls‑to‑action on `zanupf.org.zw` for:
      - Downloading/using the **ZANU–PF Constitution App**.
      - Accessing the **ZANU PF Academy**.
      - Joining structured **opinion dialogue** and surveys.
    - Use QR codes and mobile‑friendly links on web and printed materials to drive users into the app.

- **Technical Integration Options**
  - Where feasible, expose dynamic content from the website to the app via:
    - JSON/RSS feeds or dedicated APIs for **news articles**, **events**, and **priority projects**.
    - Simple caching on the app side to support offline access to recent news items and announcements.
  - Ensure that any integration:
    - Respects existing CMS/editor workflows (no duplication of content entry).
    - Is compatible with the security posture of both the public website and the app backend.

- **Coordinated Communications Strategy**
  - Treat the website and app as **complementary channels**:
    - Website: broad, public‑facing communications and archival of official information.
    - App: deep constitutional engagement, structured learning (Academy), and targeted youth dialogue.
  - Align release timelines for major campaigns and content:
    - When a new campaign or constitutional education drive launches, update both the website and app banners, Academy modules, and dialogue topics in a coordinated way.

---

### 14. First‑Run Government Setup Wizard

- **Purpose**
  - Provide a guided **first‑run setup wizard** when the backend is deployed on government / party servers for the first time.
  - Ensure that critical configuration, security, and content seeding are done correctly and consistently without manual database edits.

- **When It Runs**
  - The wizard is available only when:
    - No application admin user exists, and/or
    - Required core configuration records are missing.
  - After successful completion, the wizard:
    - Marks the system as initialised and disables itself (re‑enable only via CLI/maintenance command).

- **Wizard Steps (High-Level)**
  - **1. System Checks**
    - Verify PHP/Laravel version and required extensions.
    - Test database connection (e.g. WAMP/MySQL using configured `zanupf` database).
    - Check cache/queue configuration (e.g. Redis or database driver).
    - Confirm file permissions for storage/logs and cache directories.
  - **2. Core Application Configuration**
    - Set application name, base URLs (public API, admin panel, Academy), and primary branding assets (logo, colours).
    - Configure timezone and locale defaults.
  - **3. Security & Network Settings**
    - Enforce HTTPS and, where applicable, configure trusted proxies/load balancers used in government networks.
    - Optional recording of allowed admin access ranges/IPs (noting that deeper restrictions may be handled at network/firewall level).
  - **4. Initial Admin & Roles Bootstrap**
    - Create the first **super‑admin** user (separate from database “root”):
      - Name, email, secure password, and contact details.
    - Seed core roles and permissions:
      - Administrator, Moderator, Content Editor, Instructor, Student/Member, and any other required roles.
    - Optionally nominate initial moderators and content editors.
  - **5. Content Seeding**
    - Import or confirm the official, structured **ZANU–PF constitution dataset** (already prepared from the PDF and amendments).
    - Optionally seed:
      - Initial ZANU PF Academy course (e.g. “Constitution Basics” with a starter module).
      - Sample but clearly marked example discussion threads (for testing, removable after go‑live).
  - **6. Notifications, Email & SMS**
    - Configure email (SMTP) and/or SMS gateways used by government/party ICT for:
      - Account verification and password reset.
      - Important notifications (Academy reminders, major constitutional updates).
    - Allow “configure later” with clear warnings if not set.
  - **7. Analytics & Logging Integration**
    - Option to specify endpoints or credentials for central logging/monitoring platforms already used by government.
    - Confirm logging levels (production vs development) and data retention preferences for logs.

- **Post‑Wizard Behaviour**
  - Redirect administrators to the normal login screen and admin dashboard.
  - Provide a short **“getting started” checklist** within the dashboard:
    - Verify imported constitution content.
    - Review initial roles and permissions.
    - Configure Academy courses and launch communications about the new platform.

- **System design (supply–demand and real-time):** See `docs/SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md` for how to use queues, caching, rate limiting, and real-time feedback to smooth the app experience under load.


