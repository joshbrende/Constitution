/** Map mobile-style banner CTAs to PWA routes. */
export function openHomeBanner(navigate, banner) {
  if (!banner || !navigate) return;

  if (banner.internalPath) {
    navigate(banner.internalPath, {
      state: banner.publication ? { publication: banner.publication } : undefined,
    });
    return;
  }

  if (banner.cta_type === 'external' && banner.cta_url) {
    window.open(banner.cta_url, '_blank', 'noopener,noreferrer');
    return;
  }

  if (banner.cta_type === 'internal') {
    const screen = banner.cta_screen;
    const tab = banner.cta_tab;
    const params = banner.cta_params || {};

    if (screen === 'Biography') {
      navigate('/home/biography', {
        state: params.publication ? { publication: params.publication } : undefined,
      });
      return;
    }
    if (screen === 'AcademyHome') {
      navigate('/home/academy');
      return;
    }
    if (screen === 'PriorityProjects') {
      navigate('/home/priority-projects');
      return;
    }
    if (screen === 'LibraryHome' || screen === 'Library') {
      navigate('/home/library');
      return;
    }
    if (screen === 'Presidium') {
      navigate('/home/presidium');
      return;
    }
    if (screen === 'Party' || screen === 'PartyProfile') {
      navigate('/home/party');
      return;
    }
    if (screen === 'PartyOrgans') {
      navigate('/home/party-organs');
      return;
    }
    if (tab === 'ConstitutionTab' || screen === 'Constitutions') {
      if (params?.doc === 'zimbabwe') {
        navigate('/constitutions?doc=zimbabwe');
      } else {
        navigate('/constitutions');
      }
      return;
    }
    if (tab === 'ChatTab') {
      navigate('/chat');
      return;
    }
  }

  if (banner.cta_url) {
    window.open(banner.cta_url, '_blank', 'noopener,noreferrer');
  }
}
