import { useEffect, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { getAttemptEligibility } from '../../api/academyApi';
import { catchMessage } from '../../lib/apiErrors';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

const RULES = [
  'Study all course modules before attempting the assessment.',
  'The timer starts when you tap Begin exam and cannot be paused.',
  'Answer every question — unanswered items count as incorrect.',
  'Do not share questions, screenshots, or answers with others.',
  'Your National ID and province must be on file before you start.',
];

export default function AssessmentBriefingPage() {
  const { assessmentId } = useParams();
  const { state } = useLocation();
  const navigate = useNavigate();
  const [eligibility, setEligibility] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getAttemptEligibility(assessmentId)
      .then(setEligibility)
      .catch((e) => setError(catchMessage(e, 'Could not load attempt status.')))
      .finally(() => setLoading(false));
  }, [assessmentId]);

  const canStart = eligibility?.can_start !== false;

  return (
    <div className="space-y-4 p-4 pb-8">
      <div className="mb-1 flex items-center gap-2">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.academyAssessment} size={22} />
        <h2 className="text-xl font-bold">{state?.assessmentTitle || 'Assessment'}</h2>
      </div>
      {state?.courseTitle ? <p className="text-sm text-app-subtle">{state.courseTitle}</p> : null}

      <div className="rounded-xl border border-app-border bg-app-surface p-4 text-sm">
        <p>Pass mark: {state?.passMark ?? 70}%</p>
        {state?.durationMinutes ? <p>Time limit: {state.durationMinutes} minutes</p> : null}
        {state?.questionsPerAttempt ? <p>Questions: {state.questionsPerAttempt} per attempt</p> : null}
        {eligibility?.max_attempts != null && (
          <p>
            Attempts: {eligibility.attempts_used ?? 0} used · {eligibility.attempts_remaining ?? 0}{' '}
            remaining
          </p>
        )}
      </div>

      {loading ? <p className="text-sm text-app-subtle">Checking eligibility…</p> : null}
      {error ? <p className="text-sm text-app-error">{error}</p> : null}

      {!loading && !canStart && (
        <div className="rounded-xl border border-red-900/50 bg-red-950/30 p-4">
          <p className="font-semibold">Cannot start yet</p>
          <p className="mt-1 text-sm text-app-subtle">
            {eligibility?.message || 'You are not eligible to start a new attempt.'}
          </p>
        </div>
      )}

      <div>
        <h3 className="mb-2 font-semibold">Rules</h3>
        <ul className="list-inside list-disc space-y-1 text-sm text-app-subtle">
          {RULES.map((r) => (
            <li key={r}>{r}</li>
          ))}
        </ul>
      </div>

      <ScaleButton
        disabled={!canStart || loading}
        onClick={() =>
          navigate(`/home/academy/assessments/${assessmentId}/exam`, {
            state: { courseTitle: state?.courseTitle },
          })
        }
        className="block w-full rounded-lg bg-app-green py-3 text-center text-sm font-semibold text-white disabled:opacity-50"
      >
        Begin exam
      </ScaleButton>
    </div>
  );
}
