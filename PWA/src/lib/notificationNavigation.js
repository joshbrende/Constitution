/** Map portal notification CTAs to PWA routes (mobile: NotificationsScreen.openNotificationTarget) */
export function openNotificationTarget(navigate, msg) {
  if (msg.application_id) {
    navigate(`/home/receipt/${msg.application_id}`);
    return;
  }
  if (msg.cta_type === 'external' && msg.cta_url) {
    window.open(msg.cta_url, '_blank', 'noopener,noreferrer');
    return;
  }
  if (msg.cta_type === 'internal' && msg.cta_screen) {
    const screen = msg.cta_screen;
    const params = msg.cta_params || {};
    if (screen === 'AcademyHome') {
      navigate('/home/academy');
    } else if (screen === 'PaymentReceipt') {
      navigate(`/home/receipt/${params.applicationId || ''}`);
    } else if (screen === 'AcademyStatus' || screen === 'Certificates') {
      navigate('/home/academy-status');
    } else if (screen === 'ChatThread' && params.threadId) {
      navigate(`/chat/threads/${params.threadId}`, {
        state: { thread: { id: params.threadId, title: params.title } },
      });
    } else if (screen === 'CourseDetail' && params.courseId) {
      navigate(`/home/academy/courses/${params.courseId}`);
    } else {
      navigate('/home/notifications');
    }
    return;
  }
  if (msg.cta_tab === 'ChatTab') {
    navigate('/chat');
  }
}
