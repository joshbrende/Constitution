import { useCallback, useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { setAuthToken } from '../api/client';
import { clearAuthTokens, getAuthToken } from '../api/authStorage';
import { getProfile } from '../api/profileApi';
import splashImage from '../assets/Gemini_Generated_Image_uiz1nvuiz1nvuiz1.png';

const IMAGE_HOLD_MS = 5000;

export default function SplashPage() {
  const navigate = useNavigate();
  const [phase, setPhase] = useState('video');
  const navTarget = useRef('/login');
  const bootstrapDone = useRef(false);
  const imageHoldDone = useRef(false);

  const tryNavigate = useCallback(() => {
    if (bootstrapDone.current && imageHoldDone.current) {
      navigate(navTarget.current, { replace: true });
    }
  }, [navigate]);

  useEffect(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reducedMotion) setPhase('image');

    async function bootstrap() {
      try {
        const token = await getAuthToken();
        if (token) {
          setAuthToken(token);
          try {
            await getProfile();
            navTarget.current = '/home';
            return;
          } catch (e) {
            if (e?.response?.status === 401) {
              await clearAuthTokens();
              setAuthToken(null);
            } else {
              navTarget.current = '/home';
            }
          }
        }
      } catch {
        // fall through to login
      } finally {
        bootstrapDone.current = true;
        tryNavigate();
      }
    }

    bootstrap();
  }, [tryNavigate]);

  useEffect(() => {
    if (phase !== 'image') return undefined;
    const t = setTimeout(() => {
      imageHoldDone.current = true;
      tryNavigate();
    }, IMAGE_HOLD_MS);
    return () => clearTimeout(t);
  }, [phase, tryNavigate]);

  function onVideoEnd() {
    setPhase('image');
  }

  return (
    <div
      className={`fixed inset-0 z-50 flex items-center justify-center ${
        phase === 'image' ? 'bg-[#f5f0e6]' : 'bg-black'
      }`}
    >
      {phase === 'video' ? (
        <video
          className="h-full w-full object-cover"
          autoPlay
          muted
          playsInline
          onEnded={onVideoEnd}
          onError={onVideoEnd}
        >
          <source src={`${import.meta.env.BASE_URL}splash-video.mp4`} type="video/mp4" />
        </video>
      ) : (
        <img
          src={splashImage}
          alt="ZANUPF"
          className="h-full w-full animate-fade-in object-contain p-4"
        />
      )}
    </div>
  );
}
