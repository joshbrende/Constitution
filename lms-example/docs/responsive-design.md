# Responsive Design

The LMS is fully responsive and optimized for all devices—mobile phones, tablets, and desktops.

## Design Philosophy

- **Mobile-first approach**: Base styles target mobile devices, enhanced for larger screens
- **Bootstrap 5.3.2**: Uses Bootstrap's responsive grid and utilities
- **Touch-friendly**: Larger tap targets, readable text sizes, accessible navigation

## Breakpoints

The system uses Bootstrap's standard breakpoints:

- **xs**: < 576px (mobile phones)
- **sm**: ≥ 576px (large phones)
- **md**: ≥ 768px (tablets)
- **lg**: ≥ 992px (desktops)
- **xl**: ≥ 1200px (large desktops)

## Key Responsive Features

### Learn View (`layouts/learn.blade.php`)

- **Sidebar navigation**: 
  - Desktop: Fixed sidebar (320px width) on the left
  - Mobile/Tablet: Hidden by default, slides in from left when menu button is tapped
  - Overlay with shadow for better visibility
- **Content area**: 
  - Responsive padding (2rem → 1rem → 0.75rem on smaller screens)
  - Font sizes scale down (1.75rem → 1.5rem → 1.25rem)
- **Bottom navigation bar**: 
  - Full width on mobile
  - Reduced height (56px → 48px) and padding on small screens
- **Course hero**: 
  - Reduced padding and min-height on mobile
  - Title font size scales appropriately

### Admin & Facilitator Layouts

- **Sidebar navigation**: 
  - Desktop: Fixed sidebar in left column
  - Mobile: Stacked above content, reduced padding and font sizes
- **Tables**: 
  - Wrapped in `.table-responsive` for horizontal scrolling on small screens
  - Captions added for accessibility

### Course Catalog (`courses/index.blade.php`)

- **Course cards**: 
  - 3 columns (lg) → 2 columns (md) → 1 column (sm/xs)
  - Responsive images with proper aspect ratios
- **Search and filters**: 
  - Stack vertically on mobile
  - Order dropdown adapts to available space

### Learner Dashboard (`learner/dashboard.blade.php`)

- **Stats cards**: 
  - 4 columns (lg) → 2 columns (sm) → 1 column (xs)
  - Icons and text scale appropriately
- **Continue learning card**: 
  - Stacks vertically on mobile
  - Button placement optimized for touch

### Tables

All tables use `.table-responsive` wrapper:
- Horizontal scroll on small screens
- Maintains readability and usability
- Accessible captions added

## Mobile-Specific Enhancements

### Navigation

- **Hamburger menu**: Visible on screens < 992px
- **Touch targets**: Minimum 44x44px for accessibility
- **Text truncation**: Long text in navigation truncated with ellipsis

### Forms

- **Input fields**: Full width on mobile
- **Buttons**: Stack vertically when needed
- **Select dropdowns**: Touch-friendly sizing

### Typography

- **Headings**: Scale down appropriately (h2: 1.75rem → 1.5rem → 1.25rem)
- **Body text**: Maintains readability (minimum 16px on mobile)
- **Small text**: Used for metadata, scales appropriately

## Testing

Test the responsive design on:

1. **Mobile devices** (320px - 767px):
   - iPhone SE (375px)
   - iPhone 12/13 (390px)
   - Android phones (360px - 414px)

2. **Tablets** (768px - 991px):
   - iPad (768px)
   - iPad Pro (1024px)

3. **Desktops** (992px+):
   - Standard desktop (1280px)
   - Large desktop (1920px)

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile Safari (iOS)
- Chrome Mobile (Android)

## Accessibility

- **ARIA labels**: Added to navigation buttons and interactive elements
- **Keyboard navigation**: Full keyboard support for all interactive elements
- **Screen readers**: Semantic HTML and proper heading hierarchy
- **Color contrast**: Meets WCAG AA standards

## Future Enhancements

- Progressive Web App (PWA) support
- Offline course viewing
- Touch gestures for navigation
- Dark mode support
