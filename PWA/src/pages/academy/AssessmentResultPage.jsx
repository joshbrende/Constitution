import { useLocation, useNavigate, useParams } from 'react-router-dom';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function AssessmentResultPage() {
  const { state } = useLocation();
  const navigate = useNavigate();
  const { assessmentId } = useParams();
  const { score, passed, courseId, courseTitle, passMark, durationMinutes, questionsPerAttempt } =
    state || {};

  if (score == null) {
    return (
      <div className="p-4">
        <p className="text-sm text-app-subtle">No result data.</p>
        <button type="button" onClick={() => navigate('/home/academy')} className="mt-2 text-app-gold">
          Back to Academy
        </button>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center p-6 text-center">
      {passed ? (
        <>
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemCheck} size={72} variant="success" />
          <h2 className="mt-4 text-xl font-bold">Congratulations!</h2>
          <p className="mt-2 text-sm text-app-subtle">
            You passed with {score}%. A payment receipt has been issued. Take it to the government
            office to pay the certificate fee.
          </p>
          <ScaleButton
            onClick={() => navigate('/home/receipt', { state: { courseId } })}
            className="mt-6 w-full max-w-xs rounded-lg bg-app-green py-3 text-sm font-semibold text-white"
          >
            View payment receipt
          </ScaleButton>
          <button
            type="button"
            onClick={() => navigate('/home/academy-status')}
            className="mt-3 text-sm text-app-gold"
          >
            Track application status
          </button>
        </>
      ) : (
        <>
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemFail} size={72} variant="danger" />
          <h2 className="mt-4 text-xl font-bold">Not passed</h2>
          <p className="mt-2 text-sm text-app-subtle">
            You scored {score}%. Review the course modules and try again when you are ready.
          </p>
          <ScaleButton
            onClick={() =>
              navigate(`/home/academy/assessments/${assessmentId}/briefing`, {
                state: { courseTitle, courseId, passMark, durationMinutes, questionsPerAttempt },
              })
            }
            className="mt-6 w-full max-w-xs rounded-lg bg-app-green py-3 text-sm font-semibold text-white"
          >
            Retake assessment
          </ScaleButton>
        </>
      )}
      <button
        type="button"
        onClick={() => navigate(`/home/academy/courses/${courseId}`)}
        className="mt-4 text-sm text-app-subtle underline"
      >
        Back to course
      </button>
    </div>
  );
}
