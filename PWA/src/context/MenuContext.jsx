import { createContext, useCallback, useContext, useMemo, useState } from 'react';

const MenuContext = createContext({
  menuOpen: false,
  setMenuOpen: () => {},
  openMenu: () => {},
  closeMenu: () => {},
});

export function MenuProvider({ children }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const openMenu = useCallback(() => setMenuOpen(true), []);
  const closeMenu = useCallback(() => setMenuOpen(false), []);
  const value = useMemo(
    () => ({ menuOpen, setMenuOpen, openMenu, closeMenu }),
    [menuOpen, openMenu, closeMenu]
  );
  return <MenuContext.Provider value={value}>{children}</MenuContext.Provider>;
}

export function useMenu() {
  return useContext(MenuContext);
}
