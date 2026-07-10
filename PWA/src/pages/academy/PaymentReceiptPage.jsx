import { useCallback, useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { getCertificateApplication, getCertificateApplications } from '../../api/academyApi';
import { apiClient, getApiRootUrl } from '../../api/client';
import { catchMessage } from '../../lib/apiErrors';
import { enrichTimeline, getAcademyStatusConfig } from '../../lib/academyStatus';
import WorkflowIcon from '../../ui/icons/WorkflowIcon';
import { WORKFLOW_ICON_KEYS } from '../../ui/icons/workflowIcons';

async function downloadReceiptPdf(applicationId, receiptNumber) {
  const baseUrl = getApiRootUrl();
  const url = `${baseUrl}/api/v1/academy/applications/${applicationId}/receipt.pdf`;
  const token = apiClient.defaults.headers.common?.Authorization;
  const res = await fetch(url, {
    headers: token ? { Authorization: token } : {},
  });
  if (!res.ok) throw new Error('Download failed');
  const blob = await res.blob();
  const blobUrl = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = blobUrl;
  a.download = `payment-receipt-${receiptNumber || applicationId}.pdf`;
  a.click();
  URL.revokeObjectURL(blobUrl);
}

/** Ported from mobile/src/screens/PaymentReceiptScreen.js */
export default function PaymentReceiptPage() {
  const { applicationId: paramId } = useParams();
  const navigate = useNavigate();
  const [application, setApplication] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [downloading, setDownloading] = useState(false);

  const load = useCallback(async () => {
    try {
      setError(null);
      if (paramId) {
        setApplication(await getCertificateApplication(paramId));
      } else {
        const apps = await getCertificateApplications();
        const pending = (apps || []).find((a) => a.status === 'payment_pending') || apps?.[0];
        setApplication(pending || null);
      }
    } catch (e) {
      setError(catchMessage(e, 'Failed to load receipt.'));
    } finally {
      setLoading(false);
    }
  }, [paramId]);

  useEffect(() => {
    load();
  }, [load]);

  async function handleDownload() {
    if (!application?.id) return;
    try {
      setDownloading(true);
      await downloadReceiptPdf(application.id, application.receipt_number);
    } catch {
      window.alert('Could not download receipt. Please try again.');
    } finally {
      setDownloading(false);
    }
  }

  if (loading) return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
  if (error) return <p className="p-4 text-sm text-app-error">{error}</p>;
  if (!application) {
    return (
      <div className="p-4 text-center">
        <p className="text-sm text-app-subtle">No payment receipt available yet.</p>
        <button type="button" onClick={() => navigate('/home/academy')} className="mt-3 text-app-gold">
          Go to Academy
        </button>
      </div>
    );
  }

  const config = getAcademyStatusConfig(application.status);
  const timeline = enrichTimeline(application.timeline);

  return (
    <div className="space-y-4 p-4 pb-8">
      <div className="mb-1 flex items-center gap-2">
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateRibbon} size={22} />
        <h2 className="text-xl font-bold">Payment receipt</h2>
      </div>
      {application.receipt_number ? (
        <p className="font-mono text-app-gold">#{application.receipt_number}</p>
      ) : null}
      <p className="text-sm" style={{ color: config.badgeText }}>
        {application.status_label || application.status}
      </p>
      <p className="text-sm text-app-subtle">{config.nextStep}</p>

      {application.fee_amount != null && (
        <p className="text-lg font-bold">
          {application.fee_currency || 'USD'} {Number(application.fee_amount).toFixed(2)}
        </p>
      )}

      <button
        type="button"
        disabled={downloading}
        onClick={handleDownload}
        className="flex w-full items-center justify-center gap-2 rounded-lg bg-app-green py-3 text-sm font-semibold text-white"
      >
        <WorkflowIcon iconKey={WORKFLOW_ICON_KEYS.certificateDownload} size={18} variant="white" />
        {downloading ? 'Downloading…' : 'Download PDF receipt'}
      </button>

      {timeline.length > 0 && (
        <div className="mt-4">
          <h3 className="mb-3 font-semibold">Progress</h3>
          {timeline.map((step, i) => (
            <div key={step.key || i} className="flex gap-3 pb-4">
              <div className="flex flex-col items-center">
                <div
                  className={`h-3 w-3 rounded-full ${
                    step.done ? 'bg-green-500' : step.current ? 'bg-app-gold' : 'bg-app-border'
                  }`}
                />
                {i < timeline.length - 1 && <div className="w-px flex-1 bg-app-border" />}
              </div>
              <div>
                <p className={`text-sm ${step.done ? 'text-green-400' : ''}`}>
                  {step.label}
                  {step.current ? ' · In progress' : ''}
                </p>
                {step.at ? (
                  <p className="text-xs text-app-muted">{new Date(step.at).toLocaleString()}</p>
                ) : null}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
