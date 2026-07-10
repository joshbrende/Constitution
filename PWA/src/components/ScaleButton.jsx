export default function ScaleButton({ children, className = '', disabled, ...props }) {
  return (
    <button
      type="button"
      disabled={disabled}
      className={`transition active:scale-[0.97] disabled:opacity-60 ${className}`}
      {...props}
    >
      {children}
    </button>
  );
}
