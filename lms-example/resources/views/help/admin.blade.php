@extends('layouts.admin')

@section('title', 'Admin Help & Support')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Admin Topics</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#overview" class="list-group-item list-group-item-action">Overview</a>
                    <a href="#dashboard" class="list-group-item list-group-item-action">Admin Dashboard</a>
                    <a href="#user-management" class="list-group-item list-group-item-action">User Management</a>
                    <a href="#course-management" class="list-group-item list-group-item-action">Course Management</a>
                    <a href="#badges-tags" class="list-group-item list-group-item-action">Badges & Tags</a>
                    <a href="#instructor-requests" class="list-group-item list-group-item-action">Instructor Requests</a>
                    <a href="#attendance" class="list-group-item list-group-item-action">Attendance Management</a>
                    <a href="#analytics" class="list-group-item list-group-item-action">Analytics & Reports</a>
                    <a href="#facilitator-ratings" class="list-group-item list-group-item-action">Facilitator Ratings</a>
                    <a href="#best-practices" class="list-group-item list-group-item-action">Best Practices</a>
                    <a href="#contact" class="list-group-item list-group-item-action">Contact Support</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="mb-4">
                <h1 class="h2 mb-2">Admin Help & Support Center</h1>
                <p class="text-muted">Welcome to the TTM Group LMS Admin Help Center. This guide covers all administrative functions and best practices for managing the learning platform.</p>
            </div>

            <!-- Overview -->
            <section id="overview" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Admin Overview</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Your Role as Administrator</h5>
                        <p>As an administrator of the TTM Group Learning Management System, you have full access to manage:</p>
                        <ul>
                            <li><strong>Users:</strong> Create, view, and manage user accounts and roles</li>
                            <li><strong>Courses:</strong> Create, edit, and manage all courses</li>
                            <li><strong>Content:</strong> Manage badges, tags, and course categories</li>
                            <li><strong>Instructors:</strong> Approve or reject facilitator requests to instruct courses</li>
                            <li><strong>Analytics:</strong> View comprehensive reports on course performance and user engagement</li>
                            <li><strong>Attendance:</strong> View and export attendance records</li>
                        </ul>

                        <h5 class="h6 mt-4">Admin Dashboard</h5>
                        <p>Your dashboard provides a quick overview of:</p>
                        <ul>
                            <li>Total courses, users, and enrollments</li>
                            <li>Recent enrollments and activity</li>
                            <li>Quick access to common tasks</li>
                            <li>Attendance summary</li>
                        </ul>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-lightbulb me-2"></i><strong>Tip:</strong> Bookmark your admin dashboard for quick access. Most administrative tasks can be accessed from the sidebar navigation.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard -->
            <section id="dashboard" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-grid-1x2 me-2 text-primary"></i>Admin Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Understanding the Dashboard</h5>
                        <p>The admin dashboard is your command center. Key sections include:</p>
                        
                        <h6 class="mt-3">Statistics Cards</h6>
                        <ul>
                            <li><strong>Courses:</strong> Total number of courses in the system</li>
                            <li><strong>Users:</strong> Total registered users</li>
                            <li><strong>Enrollments:</strong> Total course enrollments</li>
                            <li><strong>Attendance:</strong> Total attendance records</li>
                        </ul>

                        <h6 class="mt-3">Quick Actions</h6>
                        <ul>
                            <li><strong>Create Course:</strong> Start a new course</li>
                            <li><strong>View Users:</strong> Access user management</li>
                            <li><strong>View Courses:</strong> Browse all courses</li>
                            <li><strong>Analytics:</strong> View course analytics</li>
                        </ul>

                        <h6 class="mt-3">Recent Activity</h6>
                        <p>Monitor recent enrollments and user activity to stay informed about platform usage.</p>
                    </div>
                </div>
            </section>

            <!-- User Management -->
            <section id="user-management" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-people me-2 text-primary"></i>User Management</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Managing Users</h5>
                        <p>Access user management from the sidebar: <strong>Admin → Users</strong></p>

                        <h6 class="mt-3">Viewing Users</h6>
                        <ol>
                            <li>Click "Users" in the admin sidebar</li>
                            <li>See a paginated list of all users</li>
                            <li>View name, email, and current role</li>
                            <li>Click "View" to see detailed user information</li>
                        </ol>

                        <h6 class="mt-3">Viewing User Details</h6>
                        <p>When viewing a user, you'll see:</p>
                        <ul>
                            <li><strong>Account Information:</strong> Email, points, badges</li>
                            <li><strong>Current Role:</strong> Student, Facilitator, or Admin</li>
                            <li><strong>Enrollments:</strong> All courses the user is enrolled in with progress</li>
                        </ul>

                        <h6 class="mt-3">Changing User Roles</h6>
                        <ol>
                            <li>Go to the user's detail page</li>
                            <li>Use the "Set role" dropdown</li>
                            <li>Select: Student, Facilitator, or Admin</li>
                            <li>Click "Update role"</li>
                            <li>Confirm the change</li>
                        </ol>

                        <h6 class="mt-3">Role Types</h6>
                        <ul>
                            <li><strong>Student:</strong> Can enroll in courses, complete units, take Knowledge Checks</li>
                            <li><strong>Facilitator:</strong> Can create and manage courses, grade assignments, respond to Q&A</li>
                            <li><strong>Admin:</strong> Full system access including user management and analytics</li>
                        </ul>

                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i><strong>Important:</strong> Changing a user's role affects their access immediately. Be careful when promoting users to Admin role.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Course Management -->
            <section id="course-management" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Course Management</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Creating Courses</h5>
                        <p>As an admin, you can create courses directly:</p>
                        <ol>
                            <li>Click "Create course" in the sidebar or dashboard</li>
                            <li>Fill in course details:
                                <ul>
                                    <li>Title (required)</li>
                                    <li>Short description</li>
                                    <li>Full description</li>
                                    <li>Featured image (optional)</li>
                                    <li>Tags (optional)</li>
                                    <li>Instructor (assign a facilitator or leave blank)</li>
                                </ul>
                            </li>
                            <li>Click "Create Course"</li>
                            <li>Add curriculum (sections, units) after creation</li>
                        </ol>

                        <h6 class="mt-3">Editing Courses</h6>
                        <ol>
                            <li>Go to "Courses" → Select a course</li>
                            <li>Click "Edit"</li>
                            <li>Modify course details</li>
                            <li>Save changes</li>
                        </ol>

                        <h6 class="mt-3">Managing Course Content</h6>
                        <p>After creating a course, add curriculum:</p>
                        <ul>
                            <li><strong>Sections:</strong> Organize content into modules (e.g., "Module 1: Introduction")</li>
                            <li><strong>Units:</strong> Add lessons, Knowledge Checks, or assignments</li>
                            <li><strong>Order:</strong> Units are displayed in the order they're added</li>
                        </ul>

                        <h6 class="mt-3">Course Status</h6>
                        <ul>
                            <li><strong>Published:</strong> Visible to all users, can be enrolled</li>
                            <li><strong>Draft:</strong> Not visible, work in progress</li>
                        </ul>

                        <h6 class="mt-3">Assigning Instructors</h6>
                        <p>You can assign facilitators to courses:</p>
                        <ol>
                            <li>Edit the course</li>
                            <li>Select an instructor from the dropdown</li>
                            <li>Save changes</li>
                        </ol>
                        <p>Alternatively, facilitators can request to instruct courses (see Instructor Requests section).</p>
                    </div>
                </div>
            </section>

            <!-- Badges & Tags -->
            <section id="badges-tags" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-award me-2 text-primary"></i>Badges & Tags</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Managing Badges</h5>
                        <p>Badges reward learners for achievements. Access from: <strong>Admin → Badges</strong></p>

                        <h6 class="mt-3">Creating Badges</h6>
                        <ol>
                            <li>Go to "Badges" in the sidebar</li>
                            <li>Click "Create Badge"</li>
                            <li>Fill in:
                                <ul>
                                    <li><strong>Name:</strong> Display name (e.g., "Course Complete")</li>
                                    <li><strong>Slug:</strong> URL-friendly identifier (auto-generated from name)</li>
                                    <li><strong>Points Required:</strong> Minimum points needed to earn</li>
                                    <li><strong>Icon:</strong> Bootstrap icon class (e.g., "bi-trophy")</li>
                                    <li><strong>Description:</strong> What the badge represents</li>
                                </ul>
                            </li>
                            <li>Click "Create Badge"</li>
                        </ol>

                        <h6 class="mt-3">Editing Badges</h6>
                        <p>Click "Edit" on any badge to modify its details. Badges are automatically awarded when users meet the requirements.</p>

                        <h5 class="h6 mt-4">Managing Tags</h5>
                        <p>Tags categorize courses. Access from: <strong>Admin → Tags</strong></p>

                        <h6 class="mt-3">Creating Tags</h6>
                        <ol>
                            <li>Go to "Tags" in the sidebar</li>
                            <li>Click "Create Tag"</li>
                            <li>Enter:
                                <ul>
                                    <li><strong>Name:</strong> Display name (e.g., "AI Fundamentals")</li>
                                    <li><strong>Slug:</strong> URL-friendly identifier</li>
                                </ul>
                            </li>
                            <li>Click "Create Tag"</li>
                        </ol>

                        <h6 class="mt-3">Using Tags</h6>
                        <p>Tags can be assigned to courses during creation or editing. Learners can filter courses by tags in the course catalog.</p>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i><strong>Tip:</strong> Use consistent naming for tags (e.g., "AI", "Finance", "HR") to help learners find related courses.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Instructor Requests -->
            <section id="instructor-requests" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Instructor Requests</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Managing Instructor Requests</h5>
                        <p>Facilitators can request to instruct courses that don't have an instructor assigned. Access from: <strong>Admin → Instructor requests</strong></p>

                        <h6 class="mt-3">Viewing Requests</h6>
                        <p>The instructor requests page shows:</p>
                        <ul>
                            <li>Course name</li>
                            <li>Facilitator name and email</li>
                            <li>Request status (Pending, Approved, Rejected)</li>
                            <li>Request date</li>
                        </ul>

                        <h6 class="mt-3">Approving Requests</h6>
                        <ol>
                            <li>Review the facilitator's request</li>
                            <li>Click "Approve"</li>
                            <li>The facilitator is assigned as the course instructor</li>
                            <li>They receive full access to manage the course</li>
                        </ol>

                        <h6 class="mt-3">Rejecting Requests</h6>
                        <ol>
                            <li>Click "Reject" on a request</li>
                            <li>The request is marked as rejected</li>
                            <li>The facilitator is notified</li>
                        </ol>

                        <h6 class="mt-3">Best Practices</h6>
                        <ul>
                            <li>Review facilitator qualifications before approving</li>
                            <li>Check if the course already has an instructor</li>
                            <li>Consider course capacity and facilitator workload</li>
                            <li>Communicate with facilitators about decisions</li>
                        </ul>

                        <div class="alert alert-success mt-4">
                            <i class="bi bi-check-circle me-2"></i><strong>Note:</strong> Once approved, facilitators can manage course content, view learner progress, grade assignments, and respond to Q&A.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Attendance -->
            <section id="attendance" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Attendance Management</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Viewing Attendance</h5>
                        <p>Access attendance records from the admin dashboard or via course pages.</p>

                        <h6 class="mt-3">Course Attendance</h6>
                        <ol>
                            <li>Go to a course page</li>
                            <li>Click "Attendance" (if available)</li>
                            <li>View all learners who submitted attendance</li>
                            <li>See submission dates and times</li>
                        </ol>

                        <h6 class="mt-3">Exporting Attendance</h6>
                        <p>Export attendance data as CSV:</p>
                        <ol>
                            <li>Go to the course attendance page</li>
                            <li>Click "Export CSV"</li>
                            <li>Download the file</li>
                            <li>Open in Excel or Google Sheets</li>
                        </ol>

                        <h6 class="mt-3">Attendance Features</h6>
                        <ul>
                            <li>Learners submit attendance through the course player</li>
                            <li>Attendance is typically required for "Day 1" or opening units</li>
                            <li>Records include learner name, email, and submission timestamp</li>
                            <li>Useful for compliance and reporting</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Analytics -->
            <section id="analytics" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Analytics & Reports</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Course Analytics</h5>
                        <p>Access from: <strong>Admin → Course analytics</strong></p>
                        <p>The analytics page shows summary cards (totals across all courses) and a per-course table:</p>
                        <ul>
                            <li><strong>Summary:</strong> Total courses, enrolled, completed, overall completion rate, quiz attempts, quiz pass rate</li>
                            <li><strong>Per course:</strong> Enrolled, Completed, Completion rate %, Quiz attempts, Quiz pass rate %, Average quiz %</li>
                            <li><strong>Quiz pass rate:</strong> Percentage of Knowledge Check attempts that passed (70%+)</li>
                        </ul>

                        <h6 class="mt-3">Using Analytics</h6>
                        <p>Analytics help you:</p>
                        <ul>
                            <li>Identify popular courses</li>
                            <li>Spot courses with low completion rates</li>
                            <li>Monitor Knowledge Check performance</li>
                            <li>Make data-driven decisions about course improvements</li>
                        </ul>

                        <h6 class="mt-3">Dashboard Statistics</h6>
                        <p>The admin dashboard provides quick stats. For detailed analytics, use the dedicated analytics page.</p>
                    </div>
                </div>
            </section>

            <!-- Facilitator Ratings -->
            <section id="facilitator-ratings" class="mb-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h4 mb-0"><i class="bi bi-star me-2 text-primary"></i>Facilitator Ratings</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="h6 mt-3">Viewing Facilitator Ratings</h5>
                        <p>Access from: <strong>Admin → Facilitator ratings</strong></p>
                        <p>See all ratings and reviews that learners have given to facilitators.</p>

                        <h6 class="mt-3">Rating Information</h6>
                        <p>Each rating includes:</p>
                        <ul>
                            <li>Facilitator name</li>
                            <li>Course name</li>
                            <li>Rating (1-5 stars)</li>
                            <li>Review text (if provided)</li>
                            <li>Learner name</li>
                            <li>Rating date</li>
                        </ul>

                        <h6 class="mt-3">Using Ratings</h6>
                        <p>Ratings help you:</p>
                        <ul>
                            <li>Identify excellent facilitators</li>
                            <li>Spot areas for improvement</li>
                            <li>Make informed decisions about instructor assignments</li>
                            <li>Recognize outstanding performance</li>
                        </ul>
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
                        <h5 class="h6 mt-3">User Management</h5>
                        <ul>
                            <li>Regularly review user roles to ensure appropriate access</li>
                            <li>Verify facilitator qualifications before approving instructor requests</li>
                            <li>Monitor user activity for security concerns</li>
                            <li>Keep user information up to date</li>
                        </ul>

                        <h5 class="h6 mt-4">Course Management</h5>
                        <ul>
                            <li>Ensure courses have clear titles and descriptions</li>
                            <li>Assign appropriate tags for easy discovery</li>
                            <li>Assign instructors to courses promptly</li>
                            <li>Review course content for quality and accuracy</li>
                            <li>Monitor course completion rates</li>
                        </ul>

                        <h5 class="h6 mt-4">Content Organization</h5>
                        <ul>
                            <li>Use consistent naming for badges and tags</li>
                            <li>Create badges that motivate learners</li>
                            <li>Organize courses into logical categories with tags</li>
                            <li>Keep badge requirements achievable but meaningful</li>
                        </ul>

                        <h5 class="h6 mt-4">Security</h5>
                        <ul>
                            <li>Limit admin access to trusted users only</li>
                            <li>Regularly review admin user list</li>
                            <li>Monitor system for unusual activity</li>
                            <li>Keep backups of important data</li>
                        </ul>

                        <h5 class="h6 mt-4">Communication</h5>
                        <ul>
                            <li>Respond promptly to instructor requests</li>
                            <li>Communicate clearly with facilitators about course assignments</li>
                            <li>Provide feedback on course performance</li>
                            <li>Keep learners informed about new courses and features</li>
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
                        <p class="lead">Need additional administrative support? Our team is here to help.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-envelope-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold">Email</h6>
                                        <p class="mb-0">
                                            <a href="mailto:{{ config('brand.contact_email', 'events@ttm-group.co.za') }}" class="text-decoration-none">{{ config('brand.contact_email', 'events@ttm-group.co.za') }}</a>
                                        </p>
                                        <small class="text-muted">For administrative support</small>
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
                            <i class="bi bi-clock me-2"></i><strong>Response Time:</strong> We aim to respond to administrative inquiries within 24-48 hours during business days.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Links -->
            <div class="card shadow-sm mt-5">
                <div class="card-body text-center">
                    <h5 class="mb-3">Quick Links</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Users</a>
                        <a href="{{ route('courses.create') }}" class="btn btn-outline-primary">Create Course</a>
                        <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-primary">Analytics</a>
                        <a href="{{ route('admin.instructor-requests.index') }}" class="btn btn-outline-primary">Instructor Requests</a>
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
