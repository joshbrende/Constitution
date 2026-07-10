import { useCallback, useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { enrolInCourse, getCourse, getEnrolment } from '../../api/academyApi';
import { getProfile } from '../../api/profileApi';
import { catchMessage } from '../../lib/apiErrors';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function CourseDetailPage() {
  const { courseId } = useParams();
  const navigate = useNavigate();
  const [course, setCourse] = useState(null);
  const [enrolment, setEnrolment] = useState(null);
  const [loading, setLoading] = useState(true);
  const [enrolling, setEnrolling] = useState(false);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    try {
      setError(null);
      const [courseRes, enrolRes] = await Promise.all([getCourse(courseId), getEnrolment(courseId)]);
      setCourse(courseRes);
      setEnrolment(enrolRes);
    } catch (e) {
      const code = e?.response?.data?.code;
      const msg = e?.response?.data?.message;
      if (code === 'MEMBERSHIP_REQUIRED' || code === 'AUDIENCE_RESTRICTED') {
        setError(msg || 'This course is not available to you yet.');
      } else {
        setError(catchMessage(e, 'Failed to load course.'));
      }
    } finally {
      setLoading(false);
    }
  }, [courseId]);

  useEffect(() => {
    load();
  }, [load]);

  async function handleEnrol() {
    try {
      setEnrolling(true);
      const me = await getProfile();
      if (!me?.national_id) {
        setError('Zimbabwe ID number is required. Please update your profile.');
        navigate('/profile');
        return;
      }
      const enrol = await enrolInCourse(courseId);
      setEnrolment(enrol);
    } catch (e) {
      setError(catchMessage(e, 'Enrolment failed.'));
    } finally {
      setEnrolling(false);
    }
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error || !course) return <p className="p-4 text-sm text-app-error">{error || 'Not found.'}</p>;

  const assessment = course.assessments?.[0];
  const isEnrolled = !!enrolment;

  return (
    <div className="space-y-4 p-4 pb-8">
      {course.grants_membership && (
        <span className="inline-flex items-center gap-1 rounded-lg bg-app-green px-2 py-1 text-xs font-semibold text-white">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyMembership} size={14} variant="white" />
          Grants membership on pass
        </span>
      )}
      <h2 className="text-xl font-bold">{course.title}</h2>
      {course.description ? <p className="text-sm text-app-subtle">{course.description}</p> : null}

      {!isEnrolled ? (
        <button
          type="button"
          disabled={enrolling}
          onClick={handleEnrol}
          className="w-full rounded-lg bg-app-green py-3 text-sm font-semibold text-white disabled:opacity-70"
        >
          {enrolling ? 'Enrolling…' : 'Enrol in this course'}
        </button>
      ) : (
        <p className="flex items-center gap-2 text-sm font-semibold text-green-400">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemCheck} size={18} variant="success" /> You
          are enrolled
        </p>
      )}

      {isEnrolled && (course.modules?.length ?? 0) > 0 && (
        <div>
          <h3 className="mb-1 flex items-center gap-2 font-bold">
            <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyCourse} size={18} />
            Course modules
          </h3>
          <p className="mb-3 text-xs text-app-subtle">Study every module before attempting the assessment.</p>
          {course.modules.map((module) => (
            <div key={module.id} className="mb-3 rounded-xl border border-app-border bg-app-surface p-3">
              <p className="mb-2 font-semibold text-app-gold">{module.title}</p>
              {(module.lessons ?? []).map((lesson) => (
                <button
                  key={lesson.id}
                  type="button"
                  onClick={() =>
                    navigate(`/home/academy/courses/${courseId}/lessons/${lesson.id}`, {
                      state: { lesson, moduleTitle: module.title },
                    })
                  }
                  className="flex w-full items-center justify-between border-t border-app-border py-2.5 text-left text-sm first:border-t-0"
                >
                  {lesson.title}
                  <WorkflowIcon
                    iconKey={WORKFLOW_ICON_KEYS.navChevronForward}
                    size={16}
                    variant="muted"
                  />
                </button>
              ))}
            </div>
          ))}
        </div>
      )}

      {assessment && isEnrolled && (
        <button
          type="button"
          onClick={() =>
            navigate(`/home/academy/assessments/${assessment.id}/briefing`, {
              state: {
                courseTitle: course.title,
                courseId: course.id,
                assessmentTitle: assessment.title,
                passMark: assessment.pass_mark ?? 70,
                durationMinutes: assessment.duration_minutes,
                questionsPerAttempt: assessment.questions_per_attempt,
              },
            })
          }
          className="flex w-full items-center gap-3 rounded-xl border border-app-border bg-app-surface p-4 text-left"
        >
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyAssessment} size={24} />
          <div className="flex-1">
            <p className="font-semibold">{assessment.title}</p>
            <p className="text-xs text-app-muted">
              Pass {assessment.pass_mark ?? 70}%
              {assessment.duration_minutes ? ` · ${assessment.duration_minutes} min` : ''}
            </p>
          </div>
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} size={18} variant="muted" />
        </button>
      )}
    </div>
  );
}
