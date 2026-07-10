import { getWorkflowIcon } from './workflowIcons';

const VARIANT_CLASS = {
  gold: 'text-app-gold',
  muted: 'text-app-muted',
  white: 'text-white',
  danger: 'text-red-400',
  success: 'text-app-green-light',
  current: 'text-current',
};

/**
 * Semantic workflow icon — mirrors mobile/src/ui/icons/WorkflowIcon.js
 * Pass iconKey (e.g. "home.party"), never a Lucide import at call sites.
 */
export default function WorkflowIcon({
  iconKey,
  variant = 'gold',
  size = 22,
  className = '',
  strokeWidth = 1.75,
  ...props
}) {
  const Icon = getWorkflowIcon(iconKey);
  const colorClass = VARIANT_CLASS[variant] ?? VARIANT_CLASS.gold;
  return (
    <Icon
      size={size}
      strokeWidth={strokeWidth}
      className={`shrink-0 ${colorClass} ${className}`.trim()}
      aria-hidden
      {...props}
    />
  );
}
