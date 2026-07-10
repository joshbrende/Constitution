import bgImage from '../assets/bg-2.jpg';

export default function AuthLayout({ children }) {
  return (
    <div className="relative isolate min-h-dvh overflow-y-auto overscroll-none">
      <div className="pointer-events-none absolute inset-0 -z-10" aria-hidden>
        <img src={bgImage} alt="" className="h-full min-h-dvh w-full object-cover object-center" />
        <div className="absolute inset-0 bg-black/45" />
      </div>

      <div className="flex min-h-dvh items-center justify-center px-4 py-8">
        <div className="animate-fade-in w-full max-w-[420px] rounded-[18px] border border-app-border/80 bg-app-bg/90 p-5 shadow-xl backdrop-blur-md">
          {children}
        </div>
      </div>
    </div>
  );
}
