import { useNetwork } from '../context/NetworkContext';

export default function ConnectivityBanner() {
  const { isOffline } = useNetwork();
  if (!isOffline) return null;

  return (
    <div className="bg-amber-900/90 px-3 py-2 text-center text-xs text-amber-100">
      You are offline. Some features may be unavailable.
    </div>
  );
}
