import { lazy, Suspense, useEffect } from 'react';
import {
  Navigate,
  Outlet,
  Route,
  createBrowserRouter,
  createRoutesFromElements,
  useNavigate,
} from 'react-router-dom';
import { GuestProvider } from './context/GuestContext';
import { AppConfigProvider } from './context/AppConfigContext';
import { NetworkProvider } from './context/NetworkContext';
import { ReaderProvider } from './context/ReaderContext';
import { ReaderDataProvider } from './context/ReaderDataContext';
import { PortalNotificationsProvider } from './context/PortalNotificationsContext';
import AppShell from './layouts/AppShell';
import SplashPage from './pages/SplashPage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ForgotPasswordPage from './pages/ForgotPasswordPage';
import HomePage from './pages/HomePage';
import ConstitutionListPage from './pages/ConstitutionListPage';
import ChapterDetailPage from './pages/ChapterDetailPage';
import SectionDetailPage from './pages/SectionDetailPage';
import BookmarksPage from './pages/BookmarksPage';
import HighlightsPage from './pages/HighlightsPage';
import NotificationAlerts from './components/NotificationAlerts';
import { setSessionExpiredHandler } from './api/client';

const PresidiumPage = lazy(() => import('./pages/PresidiumPage'));
const BiographyPage = lazy(() =>
  import('./pages/PresidiumPage').then((m) => ({ default: m.BiographyPage }))
);
const ProfilePage = lazy(() => import('./pages/ProfilePage'));
const MembersDirectoryPage = lazy(() => import('./pages/MembersDirectoryPage'));
const StaticPage = lazy(() => import('./pages/StaticPage'));
const AboutPage = lazy(() => import('./pages/AboutPage'));
const ComingSoonPage = lazy(() => import('./pages/ComingSoonPage'));
const NotificationsPage = lazy(() => import('./pages/NotificationsPage'));
const LibraryPage = lazy(() => import('./pages/LibraryPage'));
const LibraryDocumentPage = lazy(() => import('./pages/LibraryDocumentPage'));
const PartyPage = lazy(() => import('./pages/PartyPage'));
const PartyLeagueDetailPage = lazy(() => import('./pages/PartyLeagueDetailPage'));
const PartyOrgansPage = lazy(() => import('./pages/PartyOrgansPage'));
const PartyOrganDetailPage = lazy(() =>
  import('./pages/PartyOrgansPage').then((m) => ({ default: m.PartyOrganDetailPage }))
);
const PriorityProjectsPage = lazy(() => import('./pages/PriorityProjectsPage'));
const PriorityProjectDetailPage = lazy(() => import('./pages/PriorityProjectDetailPage'));
const AcademyPage = lazy(() => import('./pages/academy/AcademyPage'));
const CourseDetailPage = lazy(() => import('./pages/academy/CourseDetailPage'));
const LessonDetailPage = lazy(() => import('./pages/academy/LessonDetailPage'));
const AssessmentBriefingPage = lazy(() => import('./pages/academy/AssessmentBriefingPage'));
const AssessmentPage = lazy(() => import('./pages/academy/AssessmentPage'));
const AssessmentResultPage = lazy(() => import('./pages/academy/AssessmentResultPage'));
const AcademyStatusPage = lazy(() => import('./pages/academy/AcademyStatusPage'));
const PaymentReceiptPage = lazy(() => import('./pages/academy/PaymentReceiptPage'));
const ChatHomePage = lazy(() => import('./pages/chat/ChatHomePage'));
const ChatChannelPage = lazy(() => import('./pages/chat/ChatChannelPage'));
const ChatThreadPage = lazy(() => import('./pages/chat/ChatThreadPage'));

function RouteFallback() {
  return <p className="p-4 text-sm text-app-subtle">Loading…</p>;
}

function Lazy({ children }) {
  return <Suspense fallback={<RouteFallback />}>{children}</Suspense>;
}

/** Providers + session-expired redirect — must sit inside the data router. */
function AppRoot() {
  const navigate = useNavigate();

  useEffect(() => {
    setSessionExpiredHandler(() => navigate('/login', { replace: true }));
  }, [navigate]);

  return (
    <NetworkProvider>
      <AppConfigProvider>
        <GuestProvider>
          <PortalNotificationsProvider>
            <NotificationAlerts />
            <ReaderProvider>
              <ReaderDataProvider>
                <Outlet />
              </ReaderDataProvider>
            </ReaderProvider>
          </PortalNotificationsProvider>
        </GuestProvider>
      </AppConfigProvider>
    </NetworkProvider>
  );
}

export const router = createBrowserRouter(
  createRoutesFromElements(
    <Route element={<AppRoot />}>
      <Route path="/" element={<Navigate to="/splash" replace />} />
      <Route path="/splash" element={<SplashPage />} />
      <Route path="/login" element={<LoginPage />} />
      <Route path="/register" element={<RegisterPage />} />
      <Route path="/forgot-password" element={<ForgotPasswordPage />} />

      <Route element={<AppShell />}>
        <Route path="/home" element={<HomePage />} handle={{ title: 'Overview' }} />
        <Route
          path="/home/notifications"
          element={
            <Lazy>
              <NotificationsPage />
            </Lazy>
          }
          handle={{ title: 'Notifications', showBack: true }}
        />
        <Route
          path="/home/presidium"
          element={
            <Lazy>
              <PresidiumPage />
            </Lazy>
          }
          handle={{ title: 'Presidium', showBack: true }}
        />
        <Route
          path="/home/biography"
          element={
            <Lazy>
              <BiographyPage />
            </Lazy>
          }
          handle={{ title: 'Biography', showBack: true }}
        />
        <Route
          path="/home/academy"
          element={
            <Lazy>
              <AcademyPage />
            </Lazy>
          }
          handle={{ title: 'Academy', showBack: true }}
        />
        <Route
          path="/home/academy/courses/:courseId"
          element={
            <Lazy>
              <CourseDetailPage />
            </Lazy>
          }
          handle={{ title: 'Course', showBack: true }}
        />
        <Route
          path="/home/academy/courses/:courseId/lessons/:lessonId"
          element={
            <Lazy>
              <LessonDetailPage />
            </Lazy>
          }
          handle={{ title: 'Lesson', showBack: true }}
        />
        <Route
          path="/home/academy/assessments/:assessmentId/briefing"
          element={
            <Lazy>
              <AssessmentBriefingPage />
            </Lazy>
          }
          handle={{ title: 'Exam briefing', showBack: true }}
        />
        <Route
          path="/home/academy/assessments/:assessmentId/exam"
          element={
            <Lazy>
              <AssessmentPage />
            </Lazy>
          }
          handle={{ title: 'Assessment', showBack: true, hideTabs: true }}
        />
        <Route
          path="/home/academy/assessments/:assessmentId/result"
          element={
            <Lazy>
              <AssessmentResultPage />
            </Lazy>
          }
          handle={{ title: 'Result', showBack: false, hideTabs: true }}
        />
        <Route
          path="/home/academy-status"
          element={
            <Lazy>
              <AcademyStatusPage />
            </Lazy>
          }
          handle={{ title: 'Academy status', showBack: true }}
        />
        <Route
          path="/home/receipt"
          element={
            <Lazy>
              <PaymentReceiptPage />
            </Lazy>
          }
          handle={{ title: 'Payment receipt', showBack: true }}
        />
        <Route
          path="/home/receipt/:applicationId"
          element={
            <Lazy>
              <PaymentReceiptPage />
            </Lazy>
          }
          handle={{ title: 'Payment receipt', showBack: true }}
        />
        <Route
          path="/home/library"
          element={
            <Lazy>
              <LibraryPage />
            </Lazy>
          }
          handle={{ title: 'Digital Library', showBack: true }}
        />
        <Route
          path="/home/library/:documentId"
          element={
            <Lazy>
              <LibraryDocumentPage />
            </Lazy>
          }
          handle={{ title: 'Document', showBack: true }}
        />
        <Route
          path="/home/party"
          element={
            <Lazy>
              <PartyPage />
            </Lazy>
          }
          handle={{ title: 'The Party', showBack: true }}
        />
        <Route
          path="/home/party/leagues/:league"
          element={
            <Lazy>
              <PartyLeagueDetailPage />
            </Lazy>
          }
          handle={{ title: 'League', showBack: true }}
        />
        <Route
          path="/home/party-organs"
          element={
            <Lazy>
              <PartyOrgansPage />
            </Lazy>
          }
          handle={{ title: 'Party Organs', showBack: true }}
        />
        <Route
          path="/home/party-organs/:organId"
          element={
            <Lazy>
              <PartyOrganDetailPage />
            </Lazy>
          }
          handle={{ title: 'Party Organ', showBack: true }}
        />
        <Route
          path="/home/priority-projects"
          element={
            <Lazy>
              <PriorityProjectsPage />
            </Lazy>
          }
          handle={{ title: 'Priority projects', showBack: true }}
        />
        <Route
          path="/home/priority-projects/:projectId"
          element={
            <Lazy>
              <PriorityProjectDetailPage />
            </Lazy>
          }
          handle={{ title: 'Project', showBack: true }}
        />
        <Route path="/constitutions" element={<ConstitutionListPage />} handle={{ title: 'Constitutions' }} />
        <Route path="/constitutions/bookmarks" element={<BookmarksPage />} handle={{ title: 'Bookmarks', showBack: true }} />
        <Route path="/constitutions/highlights" element={<HighlightsPage />} handle={{ title: 'Highlights', showBack: true }} />
        <Route path="/constitutions/chapters/:chapterId" element={<ChapterDetailPage />} handle={{ title: 'Chapter', showBack: true }} />
        <Route
          path="/constitutions/sections/:sectionId"
          element={<SectionDetailPage />}
          handle={{ title: 'Section', showBack: true, hideTabs: true }}
        />
        <Route
          path="/profile"
          element={
            <Lazy>
              <ProfilePage />
            </Lazy>
          }
          handle={{ title: 'Profile' }}
        />
        <Route
          path="/members"
          element={
            <Lazy>
              <MembersDirectoryPage />
            </Lazy>
          }
          handle={{ title: 'Members', showBack: true }}
        />
        <Route
          path="/about"
          element={
            <Lazy>
              <AboutPage />
            </Lazy>
          }
          handle={{ title: 'About', showBack: true }}
        />
        <Route
          path="/pages/:slug"
          element={
            <Lazy>
              <StaticPage />
            </Lazy>
          }
          handle={{ title: 'Page', showBack: true }}
        />
        <Route
          path="/chat"
          element={
            <Lazy>
              <ChatHomePage />
            </Lazy>
          }
          handle={{ title: 'Chat' }}
        />
        <Route
          path="/chat/channels/:channelId"
          element={
            <Lazy>
              <ChatChannelPage />
            </Lazy>
          }
          handle={{ title: 'Channel', showBack: true }}
        />
        <Route
          path="/chat/threads/:threadId"
          element={
            <Lazy>
              <ChatThreadPage />
            </Lazy>
          }
          handle={{ title: 'Chat', showBack: true, hideTabs: true }}
        />
        <Route
          path="/coming-soon"
          element={
            <Lazy>
              <ComingSoonPage />
            </Lazy>
          }
          handle={{ title: 'Coming soon', showBack: true }}
        />
      </Route>

      <Route path="*" element={<Navigate to="/splash" replace />} />
    </Route>
  ),
  { basename: '/app' }
);
