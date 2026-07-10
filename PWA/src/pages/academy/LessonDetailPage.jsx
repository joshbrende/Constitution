import { useEffect, useState } from 'react';
import { Link, useLocation, useNavigate, useParams } from 'react-router-dom';
import { getCourse } from '../../api/academyApi';
import { catchMessage } from '../../lib/apiErrors';

function findLesson(course, lessonId) {
  if (!course?.modules || !lessonId) return null;
  const id = Number(lessonId);
  for (const module of course.modules) {
    const lesson = (module.lessons ?? []).find((item) => Number(item.id) === id);
    if (lesson) {
      return { lesson, moduleTitle: module.title };
    }
  }
  return null;
}

export default function LessonDetailPage() {
  const { courseId, lessonId } = useParams();
  const { state } = useLocation();
  const navigate = useNavigate();
  const [lesson, setLesson] = useState(state?.lesson ?? null);
  const [moduleTitle, setModuleTitle] = useState(state?.moduleTitle ?? null);
  const [loading, setLoading] = useState(!state?.lesson);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (state?.lesson) return;
    setLoading(true);
    setError(null);
    getCourse(courseId)
      .then((course) => {
        const match = findLesson(course, lessonId);
        if (!match) {
          setError('Lesson not found in this course.');
          return;
        }
        setLesson(match.lesson);
        setModuleTitle(match.moduleTitle);
      })
      .catch((e) => setError(catchMessage(e, 'Failed to load lesson.')))
      .finally(() => setLoading(false));
  }, [courseId, lessonId, state?.lesson]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) {
    return (
      <div className="p-4">
        <p className="text-sm text-app-error">{error}</p>
        <Link to={`/home/academy/courses/${courseId}`} className="mt-3 inline-block text-sm text-app-gold">
          Back to course
        </Link>
      </div>
    );
  }

  const content = lesson?.content?.trim();

  return (
    <article className="p-4 pb-8">
      {moduleTitle ? <p className="mb-1 text-xs text-app-muted">{moduleTitle}</p> : null}
      <h2 className="mb-4 text-xl font-bold">{lesson?.title || 'Lesson'}</h2>
      {content ? (
        <p className="whitespace-pre-wrap text-base leading-relaxed text-gray-200">{content}</p>
      ) : (
        <p className="text-sm italic text-app-muted">No content available for this lesson yet.</p>
      )}
      <button
        type="button"
        onClick={() => navigate(`/home/academy/courses/${courseId}`)}
        className="mt-6 text-sm text-app-gold underline"
      >
        Back to course
      </button>
    </article>
  );
}
