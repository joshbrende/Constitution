import { Component } from 'react';

export default class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { error: null };
  }

  static getDerivedStateFromError(error) {
    return { error };
  }

  render() {
    if (this.state.error) {
      return (
        <div className="flex min-h-dvh flex-col items-center justify-center gap-3 bg-app-bg px-6 text-center text-app-text">
          <p className="text-lg font-semibold">Something went wrong</p>
          <p className="max-w-sm text-sm text-app-subtle">
            {this.state.error?.message || 'The app failed to render.'}
          </p>
          <button
            type="button"
            className="rounded-full bg-app-green px-4 py-2 text-sm font-semibold text-white"
            onClick={() => window.location.assign(`${import.meta.env.BASE_URL}`)}
          >
            Reload app
          </button>
        </div>
      );
    }
    return this.props.children;
  }
}
