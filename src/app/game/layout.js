export const metadata = {
  title: 'Arrow Master - 3D Shooting Game',
  description: 'Addictive 3D arrow shooting game. Aim, shoot, hit targets! Bullseye combos for mega points.',
  keywords: 'arrow game, 3d game, shooting game, archery game, android game, mobile game',
  openGraph: {
    title: 'Arrow Master 3D',
    description: 'Play the most addictive arrow shooting game!',
    type: 'website',
  },
  viewport: {
    width: 'device-width',
    initialScale: 1,
    maximumScale: 1,
    userScalable: false,
  },
  themeColor: '#0a0a1a',
  appleWebApp: {
    capable: true,
    statusBarStyle: 'black-translucent',
    title: 'Arrow Master',
  },
};

export default function GameLayout({ children }) {
  return children;
}
