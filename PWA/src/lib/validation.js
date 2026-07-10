/** Ported from mobile/src/lib/validation.js (Phase 1 schemas) */
import { z } from 'zod';

export function isValidZimbabweNationalId(value) {
  if (value == null || value === '') return true;
  const normalized = String(value).replace(/[\s-]+/g, '');
  return /^\d{2}\d{4,7}[A-Za-z]\d{2}$/.test(normalized);
}

const personName = z
  .string()
  .trim()
  .min(1, 'Name is required.')
  .max(255, 'Name must be 255 characters or fewer.');

const emailField = z
  .string()
  .trim()
  .min(1, 'Email is required.')
  .email('Please enter a valid email address.')
  .max(255, 'Email must be 255 characters or fewer.')
  .transform((v) => v.toLowerCase());

const passwordField = z.string().min(8, 'Password must be at least 8 characters.');

export const loginSchema = z.object({
  email: emailField,
  password: z.string().min(1, 'Password is required.'),
});

export const registerSchema = z
  .object({
    name: personName,
    surname: personName,
    email: emailField,
    password: passwordField,
    password_confirmation: z.string().min(1, 'Please confirm your password.'),
    accept_terms: z.literal(true, {
      errorMap: () => ({ message: 'You must agree to the terms and privacy policy.' }),
    }),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match.',
    path: ['password_confirmation'],
  });

export const forgotPasswordSchema = z.object({
  email: emailField,
});

export const profileMemberSaveSchema = z.object({
  national_id: z
    .string()
    .trim()
    .min(1, 'Your Zimbabwe ID number is required to become a member and to take courses.')
    .max(32, 'National ID must be 32 characters or fewer.')
    .refine(isValidZimbabweNationalId, {
      message: 'Enter a valid Zimbabwe National ID (e.g. 08-2047823Q29).',
    }),
  province_id: z.number().int().positive().nullable().optional(),
});

export const dialogueMessageSchema = z.object({
  body: z
    .string()
    .trim()
    .min(1, 'Message cannot be empty.')
    .max(4000, 'Message must be 4000 characters or fewer.'),
});

function getFirstError(error) {
  if (!error?.issues?.length) return 'Validation failed.';
  return error.issues[0].message;
}

function getFieldErrors(error) {
  const errors = {};
  if (!error?.issues) return errors;
  for (const issue of error.issues) {
    const path = issue.path.join('.');
    if (path && !errors[path]) errors[path] = issue.message;
  }
  return errors;
}

export function validateForm(schema, data) {
  const result = schema.safeParse(data);
  if (result.success) return { success: true, data: result.data };
  return {
    success: false,
    firstError: getFirstError(result.error),
    fieldErrors: getFieldErrors(result.error),
  };
}

export function inputBorderClass(fieldError) {
  return fieldError ? 'border-red-400' : 'border-app-border';
}
