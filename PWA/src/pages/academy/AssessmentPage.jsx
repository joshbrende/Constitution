import { useCallback, useEffect, useRef, useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { getAssessment, startAttempt, submitAttempt } from '../../api/academyApi';
import { getProfile } from '../../api/profileApi';
import { catchMessage } from '../../lib/apiErrors';

function formatCountdown(totalSeconds) {
  const seconds = Math.max(0, totalSeconds);
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m}:${String(s).padStart(2, '0')}`;
}

function resolveSecondsRemaining(attempt, assessment) {
  if (typeof attempt?.seconds_remaining === 'number') return attempt.seconds_remaining;
  if (attempt?.deadline_at) {
    return Math.max(0, Math.floor((new Date(attempt.deadline_at).getTime() - Date.now()) / 1000));
  }
  const minutes = assessment?.duration_minutes;
  if (minutes && attempt?.started_at) {
    const end = new Date(attempt.started_at).getTime() + minutes * 60 * 1000;
    return Math.max(0, Math.floor((end - Date.now()) / 1000));
  }
  return null;
}

/** Ported from mobile/src/screens/AssessmentScreen.js */
export default function AssessmentPage() {
  const { assessmentId } = useParams();
  const { state } = useLocation();
  const navigate = useNavigate();
  const [assessment, setAssessment] = useState(null);
  const [attempt, setAttempt] = useState(null);
  const [answers, setAnswers] = useState({});
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState(null);
  const [secondsLeft, setSecondsLeft] = useState(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const autoSubmitRef = useRef(false);
  const answersRef = useRef({});
  const assessmentRef = useRef(null);
  const attemptRef = useRef(null);

  useEffect(() => {
    answersRef.current = answers;
  }, [answers]);
  useEffect(() => {
    assessmentRef.current = assessment;
  }, [assessment]);
  useEffect(() => {
    attemptRef.current = attempt;
  }, [attempt]);

  const submitAnswers = useCallback(
    async (force = false) => {
      const currentAssessment = assessmentRef.current;
      const currentAttempt = attemptRef.current;
      const currentAnswers = answersRef.current;
      if (!currentAssessment || !currentAttempt || submitting) return;

      const questions = currentAssessment?.questions ?? [];
      const allAnswered = questions.every((q) => currentAnswers[q.id] != null);
      if (!allAnswered && !force) {
        window.alert('Please answer all questions before submitting.');
        return;
      }

      try {
        setSubmitting(true);
        const payload = questions.map((q) => ({
          question_id: q.id,
          option_id: currentAnswers[q.id] ?? null,
        }));
        const res = await submitAttempt(currentAttempt.id, payload);
        navigate(`/home/academy/assessments/${assessmentId}/result`, {
          replace: true,
          state: {
            score: res.score,
            passed: res.passed,
            courseTitle: state?.courseTitle,
            courseId: currentAssessment.course_id,
            assessmentId: currentAssessment.id,
            passMark: currentAssessment.pass_mark ?? 70,
            durationMinutes: currentAssessment.duration_minutes,
            questionsPerAttempt: currentAssessment.questions_per_attempt,
          },
        });
      } catch (e) {
        setError(catchMessage(e, 'Failed to submit.'));
        setSubmitting(false);
      }
    },
    [assessmentId, navigate, state?.courseTitle, submitting]
  );

  useEffect(() => {
    async function init() {
      try {
        const me = await getProfile();
        if (!me?.national_id) {
          setError('Zimbabwe ID required. Update your profile.');
          navigate('/profile');
          return;
        }
        if (!me?.province_id) {
          setError('Province required. Update your profile.');
          navigate('/profile');
          return;
        }
        const assessmentRes = await getAssessment(assessmentId);
        setAssessment(assessmentRes);
        const attemptRes = await startAttempt(assessmentId, assessmentRes?.question_set_token);
        setAttempt(attemptRes);
        setSecondsLeft(resolveSecondsRemaining(attemptRes, assessmentRes));
      } catch (e) {
        setError(catchMessage(e, 'Could not start assessment.'));
      } finally {
        setLoading(false);
      }
    }
    init();
  }, [assessmentId, navigate]);

  useEffect(() => {
    if (secondsLeft == null) return undefined;
    if (secondsLeft <= 0 && !autoSubmitRef.current) {
      autoSubmitRef.current = true;
      submitAnswers(true);
      return undefined;
    }
    const t = setInterval(() => {
      setSecondsLeft((prev) => (prev == null ? prev : Math.max(0, prev - 1)));
    }, 1000);
    return () => clearInterval(t);
  }, [secondsLeft, submitAnswers]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Starting exam…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;

  const questions = assessment?.questions ?? [];
  const question = questions[currentIndex];
  const progress = questions.length ? ((currentIndex + 1) / questions.length) * 100 : 0;

  return (
    <div className="flex min-h-full flex-col p-4 pb-8">
      <div className="mb-4 flex items-center justify-between">
        <span className="text-sm text-app-subtle">
          Question {currentIndex + 1} of {questions.length}
        </span>
        {secondsLeft != null && (
          <span className={`font-mono text-sm ${secondsLeft < 60 ? 'text-red-400' : 'text-app-gold'}`}>
            {formatCountdown(secondsLeft)}
          </span>
        )}
      </div>
      <div className="mb-4 h-1 overflow-hidden rounded-full bg-app-surface">
        <div className="h-full bg-app-green transition-all" style={{ width: `${progress}%` }} />
      </div>

      {question && (
        <>
          <h2 className="mb-4 text-lg font-semibold">{question.prompt}</h2>
          <div className="space-y-2">
            {(question.options ?? []).map((opt) => (
              <button
                key={opt.id}
                type="button"
                onClick={() => setAnswers((prev) => ({ ...prev, [question.id]: opt.id }))}
                className={`block w-full rounded-lg border p-3 text-left text-sm ${
                  answers[question.id] === opt.id
                    ? 'border-app-gold bg-app-gold/10'
                    : 'border-app-border bg-app-surface'
                }`}
              >
                {opt.text}
              </button>
            ))}
          </div>
        </>
      )}

      <div className="mt-auto flex gap-2 pt-6">
        <button
          type="button"
          disabled={currentIndex === 0}
          onClick={() => setCurrentIndex((i) => i - 1)}
          className="flex-1 rounded-lg border border-app-border py-2 text-sm disabled:opacity-40"
        >
          Previous
        </button>
        {currentIndex < questions.length - 1 ? (
          <button
            type="button"
            onClick={() => setCurrentIndex((i) => i + 1)}
            className="flex-1 rounded-lg bg-app-surface py-2 text-sm font-semibold"
          >
            Next
          </button>
        ) : (
          <button
            type="button"
            disabled={submitting}
            onClick={() => submitAnswers(false)}
            className="flex-1 rounded-lg bg-app-green py-2 text-sm font-semibold text-white disabled:opacity-70"
          >
            {submitting ? 'Submitting…' : 'Submit'}
          </button>
        )}
      </div>
    </div>
  );
}
