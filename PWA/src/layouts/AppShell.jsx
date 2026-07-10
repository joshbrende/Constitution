import { Outlet, useMatches, useOutletContext } from 'react-router-dom';
import { MenuProvider } from '../context/MenuContext';
import AppHeader from '../components/AppHeader';
import BottomTabs from '../components/BottomTabs';
import SideMenu from '../components/SideMenu';
import ConnectivityBanner from '../components/ConnectivityBanner';
import GuestBanner from '../components/GuestBanner';
import InstallPrompt from '../components/InstallPrompt';

export function useAppLayout() {
  return useOutletContext();
}

export default function AppShell() {
  const matches = useMatches();
  const matchWithHandle = [...matches].reverse().find((m) => m.handle?.title);
  const title = matchWithHandle?.handle?.title ?? 'ZANUPF';
  const showBack = Boolean(matchWithHandle?.handle?.showBack);
  const hideTabs = Boolean(matchWithHandle?.handle?.hideTabs);

  return (
    <MenuProvider>
      <div className="app-shell flex min-h-dvh flex-col pb-16">
        <ConnectivityBanner />
        <GuestBanner />
        <InstallPrompt />
        <AppHeader title={title} showBack={showBack} showMenu={!showBack} />
        <main className="flex-1 overflow-y-auto">
          <Outlet context={{ title, showBack }} />
        </main>
        {!hideTabs ? <BottomTabs /> : null}
        <SideMenu />
      </div>
    </MenuProvider>
  );
}
