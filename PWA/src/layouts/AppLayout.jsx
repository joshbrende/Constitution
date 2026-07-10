import { Outlet } from 'react-router-dom';
import { MenuProvider } from '../context/MenuContext';
import AppHeader from './AppHeader';
import BottomTabs from './BottomTabs';
import SideMenu from './SideMenu';
import ConnectivityBanner from './ConnectivityBanner';
import GuestBanner from './GuestBanner';

export default function AppLayout({ title = 'ZANUPF', showBack = false, hideTabs = false }) {
  return (
    <MenuProvider>
      <div className="app-shell flex min-h-dvh flex-col pb-16">
        <ConnectivityBanner />
        <GuestBanner />
        <AppHeader title={title} showBack={showBack} />
        <main className="flex-1 overflow-y-auto">
          <Outlet />
        </main>
        {!hideTabs ? <BottomTabs /> : null}
        <SideMenu />
      </div>
    </MenuProvider>
  );
}
