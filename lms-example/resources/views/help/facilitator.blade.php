@extends('layouts.facilitator')

@section('title', 'Facilitator Help & Support')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Facilitator Topics</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#overview" class="list-group-item list-group-item-action">Overview</a>
                    <a href="#dashboard" class="list-group-item list-group-item-action">Facilitator Dashboard</a>
                    <a href="#creating-courses" class="list-group-item list-group-item-action">Creating Courses</a>
                    <a href="#course-content" class="list-group-item list-group-item-action">Course Content</a>
                    <a href="#knowledge-checks" class="list-group-item list-group-item-action">Knowledge Checks</a>
                    <a href="#assignments" class="list-group-item list-group-item-action">Grading Assignments</a>
                    <a href="#learner-management" class="list-group-item list-group-item-action">Learner Management</a>
                    <a href="#qa-chat" class="list-group-item list-group-item-action">Q&A & Chat</a>
                    <a href="#stats-analytics" class="list-group-item list-group-item-action">Stats & Analytics</a>
                    <a href="#instructor-requests" class="list-group-item list-group-item-action">Requesting Courses</a>
                    <a href="#best-practices" class="list-group-item list-group-item-action">Best Practices</a>
                    <a href="#contact" class="list-group-item list-group-item-action">Contact Support</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="mb-4">
                <h1 class="h2 mb-2">Facilitator Help & Support Center</h1>
                <p class="text-muted">Welcome to the TTM Group LMS Facilitator Help Center. This guide covers everything you need to create, manage, and facilitate courses effectively.</p>
            </div>

            <!-- Overview -->
            <section id="overview" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Facilitator Overview</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Your Role as Facilitator</h5>
                        <p>As a facilitator in the TTM Group Learning Management System, you can:</p>
                        <ul>
                            <li><strong>Create Courses:</strong> Design and build engaging learning experiences</li>
                            <li><strong>Manage Content:</strong> Add lessons, Knowledge Checks, and assignments</li>
                            <li><strong>Monitor Progress:</strong> Track learner progress and engagement</li>
                            <li><strong>Grade Assignments:</strong> Review and provide feedback on submissions</li>
                            <li><strong>Engage Learners:</strong> Answer questions and post announcements</li>
                            <li><strong>View Analytics:</strong> Access stats on course performance</li>
                        </ul>

                        <h5 class="h6 mt-4">Getting Started</h5>
                        <p>If you're new to facilitating:</p>
                        <ol>
                            <li>Review your facilitator dashboard</li>
                            <li>Create your first course or request to instruct an existing one</li>
                            <li>Add course content (sections, units, Knowledge Checks)</li>
                            <li>Publish your course and start engaging with learners</li>
                        </ol>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-lightbulb me-2"></i><strong>Tip:</strong> Start with one course to familiarize yourself with the platform, then expand to multiple courses.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard -->
            <section id="dashboard" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-grid-1x2 me-2 text-primary"></i>Facilitator Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Understanding Your Dashboard</h5>
                        <p>Your facilitator dashboard provides a quick overview of your courses and activity.</p>
                        
                        <h6 class="mt-3">Key Metrics</h6>
                        <ul>
                            <li><strong>Courses:</strong> Number of courses you're instructing</li>
                            <li><strong>Enrollments:</strong> Total learners enrolled in your courses</li>
                        </ul>

                        <h6 class="mt-3">Recent Enrollments</h6>
                        <p>See the latest learners who enrolled in your courses, including their names and enrollment dates.</p>

                        <h6 class="mt-3">Courses Available for Instructing</h6>
                        <p>If you're not an admin, you'll see courses without instructors. You can request to facilitate these courses.</p>

                        <h6 class="mt-3">Quick Actions</h6>
                        <ul>
                            <li>Create a new course</li>
                            <li>View all your courses</li>
                            <li>Access stats and analytics</li>
                            <li>View submissions requiring grading</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Creating Courses -->
            <section id="creating-courses" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Creating Courses</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Course Creation Process</h5>
                        <ol>
                            <li>Click "Create course" in the sidebar or dashboard</li>
                            <li>Fill in course details:
                                <ul>
                                    <li><strong>Title:</strong> Clear, descriptive course name (required)</li>
                                    <li><strong>Short Description:</strong> Brief overview (appears in course catalog)</li>
                                    <li><strong>Description:</strong> Full course description</li>
                                    <li><strong>Featured Image:</strong> Upload an image to make your course stand out</li>
                                    <li><strong>Tags:</strong> Select or create tags for categorization</li>
                                </ul>
                            </li>
                            <li>Click "Create Course"</li>
                            <li>You'll be redirected to edit the course and add content</li>
                        </ol>

                        <h6 class="mt-3">Course Best Practices</h6>
                        <ul>
                            <li>Use clear, descriptive titles</li>
                            <li>Write compelling descriptions that explain value</li>
                            <li>Choose relevant tags to help learners find your course</li>
                            <li>Use high-quality featured images</li>
                            <li>Organize content logically into modules</li>
                        </ul>

                        <h6 class="mt-3">Course Status</h6>
                        <ul>
                            <li><strong>Draft:</strong> Work in progress, not visible to learners</li>
                            <li><strong>Published:</strong> Visible in course catalog, learners can enroll</li>
                        </ul>

                        <div class="alert alert-success mt-4">
                            <i class="bi bi-check-circle me-2"></i><strong>Remember:</strong> You can edit courses anytime, even after publishing. Changes are reflected immediately.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Course Content -->
            <section id="course-content" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-file-text me-2 text-primary"></i>Course Content Management</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Adding Curriculum</h5>
                        <p>After creating a course, add curriculum from the course edit page:</p>

                        <h6 class="mt-3">Sections (Modules)</h6>
                        <p>Sections organize your course into logical modules:</p>
                        <ol>
                            <li>Click "Add Section"</li>
                            <li>Enter section title (e.g., "Module 1: Introduction")</li>
                            <li>Sections appear in the course sidebar</li>
                        </ol>

                        <h6 class="mt-3">Units</h6>
                        <p>Units are individual pieces of content within sections:</p>
                        <ul>
                            <li><strong>Lessons:</strong> Content units with text, images, videos</li>
                            <li><strong>Knowledge Checks:</strong> Quizzes to test understanding</li>
                            <li><strong>Assignments:</strong> Tasks requiring submission</li>
                        </ul>

                        <h6 class="mt-3">Adding Units</h6>
                        <ol>
                            <li>Select a section (or create one)</li>
                            <li>Click "Add Unit"</li>
                            <li>Choose unit type: Lesson, Knowledge Check, or Assignment</li>
                            <li>Enter title and content</li>
                            <li>Save the unit</li>
                        </ol>

                        <h6 class="mt-3">Editing Units</h6>
                        <ol>
                            <li>Go to your course</li>
                            <li>Click "Edit" on any unit</li>
                            <li>Modify content, title, or settings</li>
                            <li>Save changes</li>
                        </ol>

                        <h6 class="mt-3">Content Types</h6>
                        <ul>
                            <li><strong>HTML Content:</strong> Rich text, images, videos, links</li>
                            <li><strong>Video:</strong> Embed videos from YouTube, Vimeo, or upload</li>
                            <li><strong>Files:</strong> Attach PDFs, documents, or other resources</li>
                        </ul>

                        <h6 class="mt-3">Content Organization Tips</h6>
                        <ul>
                            <li>Start with an introduction section</li>
                            <li>Break content into digestible modules</li>
                            <li>Include Knowledge Checks after major topics</li>
                            <li>End with a summary or next steps</li>
                            <li>Use clear, descriptive unit titles</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Knowledge Checks -->
            <section id="knowledge-checks" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Knowledge Checks</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Creating Knowledge Checks</h5>
                        <p>Knowledge Checks (quizzes) test learner understanding and unlock progression.</p>

                        <h6 class="mt-3">Setting Up a Knowledge Check</h6>
                        <ol>
                            <li>Add a unit and select "Knowledge Check" type</li>
                            <li>Enter the Knowledge Check title</li>
                            <li>Click "Edit Quiz" to add questions</li>
                            <li>Configure quiz settings:
                                <ul>
                                    <li><strong>Passing Score:</strong> Default is 70%</li>
                                    <li><strong>Randomize Questions:</strong> Shuffle question order</li>
                                    <li><strong>Unlock Next Module:</strong> Require passing to proceed</li>
                                </ul>
                            </li>
                        </ol>

                        <h6 class="mt-3">Question Types</h6>
                        <ul>
                            <li><strong>Multiple Choice:</strong> One correct answer from several options</li>
                            <li><strong>True/False:</strong> Simple true or false questions</li>
                            <li><strong>Short Answer:</strong> Text input with case-insensitive matching</li>
                        </ul>

                        <h6 class="mt-3">Adding Questions</h6>
                        <ol>
                            <li>In quiz editor, click "Add Question"</li>
                            <li>Select question type</li>
                            <li>Enter question text</li>
                            <li>Add answer options (for multiple choice)</li>
                            <li>Mark correct answer(s)</li>
                            <li>Save question</li>
                        </ol>

                        <h6 class="mt-3">Best Practices</h6>
                        <ul>
                            <li>Write clear, unambiguous questions</li>
                            <li>Use realistic distractors for multiple choice</li>
                            <li>Test understanding, not memorization</li>
                            <li>Provide feedback through question explanations</li>
                            <li>Keep questions focused on key learning objectives</li>
                        </ul>

                        <h6 class="mt-3">Viewing Results</h6>
                        <p>Access Knowledge Check results from:</p>
                        <ul>
                            <li><strong>Knowledge Check Results:</strong> See all attempts and scores</li>
                            <li><strong>Knowledge Check Stats:</strong> View statistics per Knowledge Check</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Assignments -->
            <section id="assignments" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Grading Assignments</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Creating Assignments</h5>
                        <ol>
                            <li>Add a unit and select "Assignment" type</li>
                            <li>Enter assignment title and instructions</li>
                            <li>Specify requirements (text response, file upload, or both)</li>
                            <li>Save the assignment</li>
                        </ol>

                        <h6 class="mt-3">Viewing Submissions</h6>
                        <p>Access submissions from: <strong>Instructing → Submissions</strong></p>
                        <p>The submissions page shows:</p>
                        <ul>
                            <li>Learner name and email</li>
                            <li>Course and assignment name</li>
                            <li>Submission date</li>
                            <li>Status (Pending, Graded)</li>
                        </ul>

                        <h6 class="mt-3">Grading Assignments</h6>
                        <ol>
                            <li>Go to Submissions page</li>
                            <li>Click "Grade" on a submission</li>
                            <li>Review the learner's work:
                                <ul>
                                    <li>Read text responses</li>
                                    <li>Download and review uploaded files</li>
                                </ul>
                            </li>
                            <li>Enter grade (score out of 100)</li>
                            <li>Add feedback comments</li>
                            <li>Click "Save Grade"</li>
                            <li>Learner receives a notification</li>
                        </ol>

                        <h6 class="mt-3">Grading Best Practices</h6>
                        <ul>
                            <li>Grade promptly to keep learners engaged</li>
                            <li>Provide constructive, specific feedback</li>
                            <li>Highlight strengths and areas for improvement</li>
                            <li>Be consistent with grading criteria</li>
                            <li>Use rubrics when possible</li>
                        </ul>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i><strong>Note:</strong> Learners receive notifications when assignments are graded. Check your notifications regularly for new submissions.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Learner Management -->
            <section id="learner-management" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-people me-2 text-primary"></i>Learner Management</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Viewing Learners</h5>
                        <p>Access learner information from your courses:</p>
                        <ol>
                            <li>Go to "Instructing" → Select a course</li>
                            <li>Click "Learners" or "View Learners"</li>
                            <li>See all enrolled learners with:
                                <ul>
                                    <li>Progress percentage</li>
                                    <li>Units completed</li>
                                    <li>Knowledge Checks completed</li>
                                    <li>Last activity date</li>
                                    <li>Status (In progress, Completed, At risk)</li>
                                </ul>
                            </li>
                        </ol>

                        <h6 class="mt-3">At-Risk Learners</h6>
                        <p>Identify learners who may need support:</p>
                        <ul>
                            <li>Filter by "At risk" status</li>
                            <li>See learners with low progress or inactivity</li>
                            <li>Reach out proactively to offer assistance</li>
                        </ul>

                        <h6 class="mt-3">Monitoring Progress</h6>
                        <p>Track learner engagement:</p>
                        <ul>
                            <li>View completion rates</li>
                            <li>Monitor Knowledge Check performance</li>
                            <li>Identify common sticking points</li>
                            <li>Adjust course content based on data</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Q&A & Chat -->
            <section id="qa-chat" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Q&A & Chat</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Managing Q&A</h5>
                        <p>Engage with learners through the Q&A feature.</p>

                        <h6 class="mt-3">Accessing Q&A</h6>
                        <p>Two ways to access:</p>
                        <ul>
                            <li><strong>From Course:</strong> While viewing a course, click "Q&A" or "Facilitator Chat"</li>
                            <li><strong>Dedicated Page:</strong> Go to "Instructing" → Select course → "Q&A"</li>
                        </ul>

                        <h6 class="mt-3">Responding to Questions</h6>
                        <ol>
                            <li>View questions from learners</li>
                            <li>Click "Reply" on a question</li>
                            <li>Type your response</li>
                            <li>Post the reply</li>
                            <li>Learner receives a notification</li>
                        </ol>

                        <h6 class="mt-3">Posting Announcements</h6>
                        <p>Share important updates:</p>
                        <ol>
                            <li>Go to Q&A page</li>
                            <li>Click "Post Announcement"</li>
                            <li>Enter announcement text</li>
                            <li>Post to all enrolled learners</li>
                        </ol>

                        <h6 class="mt-3">Managing Status</h6>
                        <ul>
                            <li>Mark questions as "Resolved" when answered</li>
                            <li>Keep "Open" for follow-up discussions</li>
                            <li>Organize by status for easier management</li>
                        </ul>

                        <h6 class="mt-3">Best Practices</h6>
                        <ul>
                            <li>Respond promptly (within 24-48 hours)</li>
                            <li>Be clear and helpful in responses</li>
                            <li>Use announcements for course-wide updates</li>
                            <li>Encourage peer learning in discussions</li>
                            <li>Check Q&A regularly</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Stats & Analytics -->
            <section id="stats-analytics" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Stats & Analytics</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Course Statistics</h5>
                        <p>Access from: <strong>Instructing → Stats</strong></p>
                        <p>View comprehensive statistics for your courses:</p>
                        <ul>
                            <li>Total enrollments</li>
                            <li>Completion rates</li>
                            <li>Average progress</li>
                            <li>Active learners</li>
                        </ul>

                        <h6 class="mt-3">Knowledge Check Statistics</h6>
                        <p>Access from: <strong>Instructing → Knowledge Check stats</strong></p>
                        <p>Per-Knowledge Check analytics:</p>
                        <ul>
                            <li>Total attempts</li>
                            <li>Pass rate</li>
                            <li>Average score</li>
                            <li>Question-level performance</li>
                        </ul>

                        <h6 class="mt-3">Knowledge Check Results</h6>
                        <p>Access from: <strong>Instructing → Knowledge Check results</strong></p>
                        <p>View individual attempts:</p>
                        <ul>
                            <li>Learner name</li>
                            <li>Score and pass/fail status</li>
                            <li>Attempt date</li>
                            <li>Question-by-question breakdown</li>
                        </ul>

                        <h6 class="mt-3">Using Analytics</h6>
                        <p>Analytics help you:</p>
                        <ul>
                            <li>Identify difficult topics</li>
                            <li>Improve course content</li>
                            <li>Recognize high-performing learners</li>
                            <li>Make data-driven improvements</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Instructor Requests -->
            <section id="instructor-requests" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Requesting to Instruct Courses</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Requesting a Course</h5>
                        <p>If a course doesn't have an instructor, you can request to facilitate it:</p>
                        <ol>
                            <li>Go to your Facilitator Dashboard</li>
                            <li>Find "Courses available for instructing"</li>
                            <li>Click "Request to instruct" on a course</li>
                            <li>Your request is sent to administrators</li>
                            <li>You'll be notified when approved or rejected</li>
                        </ol>

                        <h6 class="mt-3">Pending Requests</h6>
                        <p>View your pending requests on the dashboard. Status updates appear there.</p>

                        <h6 class="mt-3">After Approval</h6>
                        <p>Once approved:</p>
                        <ul>
                            <li>You become the course instructor</li>
                            <li>You can edit course content</li>
                            <li>You can view learner progress</li>
                            <li>You can grade assignments</li>
                            <li>You can manage Q&A</li>
                        </ul>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i><strong>Note:</strong> Admins can also directly assign you to courses. You'll see these in your "Instructing" section.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Best Practices -->
            <section id="best-practices" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-check2-circle me-2 text-primary"></i>Best Practices</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Course Design</h5>
                        <ul>
                            <li>Structure content logically and progressively</li>
                            <li>Break complex topics into digestible modules</li>
                            <li>Include clear learning objectives</li>
                            <li>Use multimedia to enhance engagement</li>
                            <li>Provide real-world examples and applications</li>
                        </ul>

                        <h5 class="h6 mt-4">Engagement</h5>
                        <ul>
                            <li>Respond to Q&A within 24-48 hours</li>
                            <li>Post regular announcements</li>
                            <li>Encourage discussion and peer learning</li>
                            <li>Recognize learner achievements</li>
                            <li>Be approachable and supportive</li>
                        </ul>

                        <h5 class="h6 mt-4">Assessment</h5>
                        <ul>
                            <li>Create clear, fair Knowledge Checks</li>
                            <li>Test understanding, not memorization</li>
                            <li>Provide timely assignment feedback</li>
                            <li>Use rubrics for consistent grading</li>
                            <li>Offer opportunities for improvement</li>
                        </ul>

                        <h5 class="h6 mt-4">Content Quality</h5>
                        <ul>
                            <li>Proofread all content before publishing</li>
                            <li>Ensure accuracy and currency</li>
                            <li>Update content regularly</li>
                            <li>Fix broken links or media</li>
                            <li>Test Knowledge Checks before publishing</li>
                        </ul>

                        <h5 class="h6 mt-4">Time Management</h5>
                        <ul>
                            <li>Set aside regular time for Q&A</li>
                            <li>Grade assignments in batches</li>
                            <li>Use analytics to identify priorities</li>
                            <li>Plan course updates in advance</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Contact -->
            <section id="contact" class="mb-5">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="h4 mb-0"><i class="bi bi-envelope me-2"></i>Contact TTM Group Support</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">Need facilitator support? Our team is here to help.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-envelope-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold">Email</h6>
                                        <p class="mb-0">
                                            <a href="mailto:{{ config('brand.contact_email', 'events@ttm-group.co.za') }}" class="text-decoration-none">{{ config('brand.contact_email', 'events@ttm-group.co.za') }}</a>
                                        </p>
                                        <small class="text-muted">For facilitator support</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-telephone-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold">Phone</h6>
                                        <p class="mb-0">
                                            <a href="tel:+27662431698" class="text-decoration-none">+27 66 243 1698</a>
                                        </p>
                                        <small class="text-muted">Business hours: Mon-Fri, 8:00-17:00 SAST</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-clock me-2"></i><strong>Response Time:</strong> We aim to respond to facilitator inquiries within 24-48 hours during business days.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Links -->
            <div class="card shadow-sm mt-5">
                <div class="card-body text-center">
                    <h5 class="mb-3">Quick Links</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('instructor.dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                        <a href="{{ route('courses.create') }}" class="btn btn-outline-primary">Create Course</a>
                        <a href="{{ route('courses.instructor') }}" class="btn btn-outline-primary">My Courses</a>
                        <a href="{{ route('instructor.stats') }}" class="btn btn-outline-primary">Stats</a>
                        <a href="{{ route('instructor.submissions.index') }}" class="btn btn-outline-primary">Submissions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.list-group-item').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });

    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.list-group-item');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .list-group-item.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    section {
        scroll-margin-top: 80px;
    }
    .sticky-top {
        z-index: 1020;
    }
    @media (max-width: 991.98px) {
        .sticky-top {
            position: relative !important;
        }
    }
</style>
@endpush
@endsection
