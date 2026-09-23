import { useLocale } from '../i18n/LocaleContext';

function Home() {
  const { t } = useLocale();
  return (
    <main className="min-h-svh bg-neutral-50 font-sans text-neutral-950 antialiased">
      <p className="mx-auto max-w-2xl px-6 py-24 text-center">
        <span className="font-display text-4xl text-forest-800">
          {t('nav.brand')}
        </span>
      </p>
    </main>
  );
}

export default Home;
