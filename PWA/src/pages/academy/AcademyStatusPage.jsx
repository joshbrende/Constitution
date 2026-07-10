import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { getCertificateApplications } from '../../api/academyApi';
import { catchMessage } from '../../lib/apiErrors';
import { getAcademyStatusConfig } from '../../lib/academyStatus';
import ScaleButton from '../../components/ScaleButton';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

export default function AcademyStatusPage() {
  const navigate = useNavigate();
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    try {
      setError(null);
      const data = await getCertificateApplications();
      setApplications(Array.isArray(data) ? data : []);
    } catch (e) {
      setError(catchMessage(e, 'Failed to load applications.'));
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) {
    return (
      <div className="p-4 text-center">
        <p className="text-sm text-app-error">{error}</p>
        <button type="button" onClick={load} className="mt-2 text-app-gold">
          Try again
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-4 p-4 pb-8">
      <div>
        <div className="mb-1 flex items-center gap-2">
          <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateRibbon} size={22} />
          <h2 className="text-xl font-bold">Academy status</h2>
        </div>
        <p className="text-sm text-app-subtle">
          Track your certificate application, payment receipt, and collection status.
        </p>
      </div>

      <ScaleButton
        onClick={() => navigate('/home/notifications')}
        className="flex w-full items-center gap-3 rounded-xl border border-app-border bg-app-surface p-3"
      >
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.systemAnnouncement} size={20} />
        <span className="flex-1 text-left font-medium">Portal messages</span>
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.navChevronForward} size={18} variant="muted" />
      </ScaleButton>

      {applications.length === 0 ? (
        <div className="rounded-xl border border-app-border bg-app-surface p-8 text-center">
          <WorkflowIcon
            iconKey={WORKFLOW_ICON_KEYS.academyAssessment}
            size={36}
            variant="muted"
            className="mx-auto mb-3"
          />
          <p className="font-semibold">No applications yet</p>
          <p className="mt-2 text-sm text-app-subtle">
            Complete the membership course and pass the assessment to receive a payment receipt.
          </p>
          <button
            type="button"
            onClick={() => navigate('/home/academy')}
            className="mt-4 rounded-full bg-app-green px-4 py-2 text-sm font-semibold text-white"
          >
            Go to Academy
          </button>
        </div>
      ) : (
        applications.map((app) => {
          const config = getAcademyStatusConfig(app.status);
          return (
            <ScaleButton
              key={app.id}
              onClick={() => navigate(`/home/receipt/${app.id}`)}
              className="block w-full rounded-xl border border-app-border bg-app-surface p-4 text-left"
            >
              <div className="mb-1 flex items-center gap-2">
                <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateRibbon} size={16} />
                <p className="font-semibold">{app.course_title || 'Certificate application'}</p>
              </div>
              <p className="text-sm" style={{ color: config.badgeText }}>
                {app.status_label || app.status}
              </p>
              <p className="mt-1 text-xs text-app-subtle">{config.nextStep}</p>
              {app.receipt_number ? (
                <p className="mt-2 font-mono text-xs text-app-gold">Receipt {app.receipt_number}</p>
              ) : null}
            </ScaleButton>
          );
        })
      )}
    </div>
  );
}
