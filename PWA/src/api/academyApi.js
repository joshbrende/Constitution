import { apiClient } from './client';

export async function getCourses() {
  const { data } = await apiClient.get('/academy/courses');
  return data.data;
}

export async function getMembershipCourse() {
  const { data } = await apiClient.get('/academy/courses/membership');
  return data.data;
}

export async function getAcademySummary() {
  const { data } = await apiClient.get('/academy/summary');
  return data.data;
}

export async function getAcademyBadges() {
  const { data } = await apiClient.get('/academy/badges');
  return data.data ?? [];
}

export async function getCourse(id) {
  const { data } = await apiClient.get(`/academy/courses/${id}`);
  return data.data;
}

export async function enrolInCourse(courseId) {
  const { data } = await apiClient.post(`/academy/courses/${courseId}/enrol`);
  return data.data;
}

export async function getEnrolment(courseId) {
  const { data } = await apiClient.get(`/academy/courses/${courseId}/enrolment`);
  return data.data;
}

export async function getAssessment(assessmentId) {
  const { data } = await apiClient.get(`/academy/assessments/${assessmentId}`);
  return data.data;
}

export async function getAttemptEligibility(assessmentId) {
  const { data } = await apiClient.get(`/academy/assessments/${assessmentId}/attempt-eligibility`);
  return data.data;
}

export async function startAttempt(assessmentId, questionSetToken) {
  const { data } = await apiClient.post(`/academy/assessments/${assessmentId}/attempts`, {
    question_set_token: questionSetToken ?? null,
  });
  return data.data;
}

export async function submitAttempt(attemptId, answers) {
  const { data } = await apiClient.post(`/academy/attempts/${attemptId}/submit`, {
    answers: answers.map((a) => ({ question_id: a.question_id, option_id: a.option_id })),
  });
  return data;
}

export async function getCertificateApplications() {
  const { data } = await apiClient.get('/academy/applications');
  return data.data ?? [];
}

export async function getCertificateApplication(applicationId) {
  const { data } = await apiClient.get(`/academy/applications/${applicationId}`);
  return data.data;
}
