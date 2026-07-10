import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  getAcademyBadges,
  getAcademySummary,
  getCourses,
  getMembershipCourse,
} from '../../api/academyApi';
import { catchMessage } from '../../lib/apiErrors';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function AcademyPage() {
  const navigate = useNavigate();
  const [courses, setCourses] = useState([]);
  const [membershipCourse, setMembershipCourse] = useState(null);
  const [badges, setBadges] = useState([]);
  const [portalSummary, setPortalSummary] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    try {
      setError(null);
      const [coursesRes, membershipRes, summaryRes] = await Promise.all([
        getCourses(),
        getMembershipCourse(),
        getAcademySummary().catch(() => null),
      ]);
      setCourses(Array.isArray(coursesRes) ? coursesRes : []);
      setMembershipCourse(membershipRes);
      setPortalSummary(summaryRes);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load academy.'));
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    if (!membershipCourse) return;
    getAcademyBadges()
      .then((data) => setBadges(Array.isArray(data) ? data : []))
      .catch(() => setBadges([]));
  }, [membershipCourse]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) {
    return (
      <div className="p-4 text-center">
        <p className="text-sm text-app-error">{error}</p>
        <button type="button" onClick={load} className="mt-3 text-sm text-app-gold">
          Try again
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <div className="mb-1 flex items-center gap-2">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.homeAcademy} size={22} />
          <h2 className="text-xl font-bold">ZANU PF Academy</h2>
        </div>
        <p className="mt-1 text-sm text-app-subtle">
          Complete courses and assessments. After passing, pay at the government office and collect
          your certificate in person.
        </p>
      </div>

      {(portalSummary?.pending_payment_applications > 0 || portalSummary?.latest_application_status) && (
        <ScaleButton
          onClick={() =>
            portalSummary?.pending_payment_applications > 0
              ? navigate('/home/receipt')
              : navigate('/home/academy-status')
          }
          className="block w-full rounded-xl border border-app-gold bg-app-surface p-4 text-left"
        >
          <div className="mb-2 flex items-center gap-2">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemAnnouncement} size={18} />
            <span className="font-semibold">
              {portalSummary?.latest_application_status_label || 'Application update'}
            </span>
          </div>
          <p className="text-sm text-app-subtle">
            {portalSummary?.pending_payment_applications > 0
              ? 'Payment required — view your receipt and pay at the designated office.'
              : 'Track your certificate application status and portal messages.'}
          </p>
          <span className="mt-2 inline-block text-sm font-semibold text-app-gold">
            {portalSummary?.pending_payment_applications > 0 ? 'View receipt →' : 'Open status →'}
          </span>
        </ScaleButton>
      )}

      {membershipCourse && (
        <ScaleButton
          onClick={() => navigate(`/home/academy/courses/${membershipCourse.id}`)}
          className="block w-full rounded-xl border border-app-green bg-app-surface p-4 text-left"
        >
          <span className="inline-flex items-center gap-1 rounded-lg bg-app-green px-2 py-0.5 text-xs font-semibold text-white">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyMembership} size={14} variant="white" />{' '}
            Membership Course
          </span>
          <p className="mt-2 font-bold">{membershipCourse.title}</p>
          <p className="line-clamp-2 text-sm text-app-subtle">
            {membershipCourse.description || 'Pass the assessment to become a member.'}
          </p>
          <span className="mt-2 inline-block text-sm font-semibold text-app-gold">Open course →</span>
        </ScaleButton>
      )}

      <h3 className="flex items-center gap-2 font-semibold">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyCourse} size={18} />
        All Courses
      </h3>
      {courses.length === 0 ? (
        <p className="rounded-xl bg-app-surface p-6 text-center text-sm text-app-muted">
          No courses available yet.
        </p>
      ) : (
        courses.map((course) => (
          <ScaleButton
            key={course.id}
            onClick={() => navigate(`/home/academy/courses/${course.id}`)}
            className="flex w-full items-center gap-3 rounded-xl border border-app-border bg-app-surface p-3 text-left"
          >
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyCourse} size={22} />
            <div className="min-w-0 flex-1">
              <p className="font-semibold">{course.title}</p>
              <p className="text-xs text-app-muted">{course.enrolments_count ?? 0} enrolled</p>
            </div>
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} size={18} variant="muted" />
          </ScaleButton>
        ))
      )}

      <h3 className="flex items-center gap-2 font-semibold">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyAchievement} size={18} />
        Achievements
      </h3>
      {badges.length === 0 ? (
        <p className="text-sm text-app-muted">No achievements configured yet.</p>
      ) : (
        badges.map((b) => (
          <div
            key={b.id}
            className="flex items-center gap-3 rounded-xl border border-app-border bg-app-surface p-3"
          >
            <div
              className={`flex h-10 w-10 items-center justify-center rounded-lg border ${
                b.unlocked ? 'border-app-gold bg-app-gold/10' : 'border-app-border opacity-50'
              }`}
            >
              <WorkflowIcon
                iconKey={WORKFLOW_ICON_KEYS.academyBadge}
                size={20}
                variant={b.unlocked ? 'gold' : 'muted'}
              />
            </div>
            <div>
              <p className={`text-sm font-bold ${!b.unlocked && 'text-app-subtle'}`}>{b.title}</p>
              <p className="text-xs text-app-muted">
                {b.unlocked ? 'Unlocked' : `Progress: ${b.progress_percent}%`}
              </p>
            </div>
          </div>
        ))
      )}
    </div>
  );
}
