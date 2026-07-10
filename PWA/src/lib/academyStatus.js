/** Ported from mobile/src/utils/academyStatus.js */
const STATUS_CONFIG = {
  payment_pending: { badgeText: '#facc15', nextStep: 'Download your receipt and pay at the designated government office.' },
  payment_confirmed: { badgeText: '#94a3b8', nextStep: 'Payment received — awaiting Presidium approval.' },
  presidium_pending: { badgeText: '#94a3b8', nextStep: 'Your application is with the Presidium for approval.' },
  presidium_approved: { badgeText: '#4ade80', nextStep: 'Approved — your certificate is being prepared.' },
  print_ready: { badgeText: '#4ade80', nextStep: 'Your certificate is queued for printing.' },
  printed: { badgeText: '#4ade80', nextStep: 'Printed — you will be notified when ready for collection.' },
  ready_for_collection: { badgeText: '#4ade80', nextStep: 'Visit the party office to collect your certificate.' },
  collected: { badgeText: '#94a3b8', nextStep: 'Certificate collected — thank you.' },
  cancelled: { badgeText: '#f87171', nextStep: 'This application was cancelled.' },
};

const DEFAULT = { badgeText: '#94a3b8', nextStep: 'Your application is being processed.' };

export function getAcademyStatusConfig(status) {
  return STATUS_CONFIG[status] || DEFAULT;
}

export function enrichTimeline(timeline) {
  if (!Array.isArray(timeline) || timeline.length === 0) return [];
  const currentIndex = timeline.findIndex((step) => !step.completed);
  const activeIndex = currentIndex === -1 ? timeline.length - 1 : currentIndex;
  return timeline.map((step, index) => ({
    ...step,
    current: index === activeIndex && !step.completed,
    done: step.completed,
  }));
}
