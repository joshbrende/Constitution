import { useLocation, useNavigate } from 'react-router-dom';

export default function ComingSoonPage() {
  const { state, pathname } = useLocation();
  const navigate = useNavigate();
  const feature =
    state?.feature ?? (pathname.includes('/chat') ? 'Chat' : 'This feature');
  const phase = state?.phase ?? (pathname.includes('/chat') ? 3 : 2);

  return (
    <div className="flex flex-col items-center justify-center px-6 py-16 text-center">
      <p className="text-sm uppercase tracking-wide text-app-gold">Phase {phase}</p>
      <h2 className="mt-2 text-xl font-bold">{feature}</h2>
      <p className="mt-3 text-sm text-app-subtle">
        This section is being ported from the mobile app. It will arrive in a future update.
      </p>
      <button
        type="button"
        onClick={() => navigate(-1)}
        className="mt-6 rounded-full bg-app-green px-6 py-2 text-sm font-semibold text-white"
      >
        Go back
      </button>
    </div>
  );
}
