import type { Component } from 'vue'
import {
  BookOpenCheck,
  BriefcaseBusiness,
  ClipboardSignature,
  GraduationCap,
  Languages,
  LayoutTemplate,
  ListChecks,
  NotebookPen,
  Palette,
  Scroll,
  UserRound,
} from 'lucide-vue-next'

export interface ResumeSection {
  id: string
  label: string
  description: string
  icon: Component
  badge?: string
}

export interface ResumeSectionGroup {
  id: string
  label: string
  items: ResumeSection[]
}

export const resumeSectionGroups: ResumeSectionGroup[] = [
  {
    id: 'profile',
    label: 'Profile',
    items: [
      {
        id: 'basic-info',
        label: 'Basic Information',
        description: 'Name, title, contact details, and location.',
        icon: UserRound,
      },
      {
        id: 'professional-summary',
        label: 'Professional Summary',
        description: 'Craft a concise summary that highlights your value.',
        icon: ClipboardSignature,
      },
      {
        id: 'focus-roles',
        label: 'Target Roles',
        description: 'Align your resume with the positions you are seeking.',
        icon: BriefcaseBusiness,
        badge: 'New',
      },
    ],
  },
  {
    id: 'experience',
    label: 'Experience',
    items: [
      {
        id: 'work-experience',
        label: 'Work Experience',
        description: 'Detail your responsibilities and impact for each role.',
        icon: NotebookPen,
      },
      {
        id: 'projects',
        label: 'Projects',
        description: 'Showcase notable projects and the results you delivered.',
        icon: LayoutTemplate,
      },
      {
        id: 'achievements',
        label: 'Achievements',
        description: 'Highlight metrics and accolades that set you apart.',
        icon: Scroll,
      },
    ],
  },
  {
    id: 'education',
    label: 'Education & Skills',
    items: [
      {
        id: 'education',
        label: 'Education',
        description: 'List degrees, certifications, and relevant coursework.',
        icon: GraduationCap,
      },
      {
        id: 'skills',
        label: 'Skill Matrix',
        description: 'Categorize technical, leadership, and soft skills.',
        icon: ListChecks,
      },
      {
        id: 'languages',
        label: 'Languages',
        description: 'Track language proficiency and fluency levels.',
        icon: Languages,
      },
    ],
  },
  {
    id: 'extras',
    label: 'Enhancements',
    items: [
      {
        id: 'certifications',
        label: 'Certifications',
        description: 'Add professional credentials and industry recognition.',
        icon: BookOpenCheck,
      },
      {
        id: 'volunteering',
        label: 'Volunteering',
        description: 'Demonstrate community involvement and leadership.',
        icon: Palette,
      },
      {
        id: 'custom-sections',
        label: 'Custom Sections',
        description: 'Create tailored sections unique to your story.',
        icon: LayoutTemplate,
        badge: 'Flexible',
      },
    ],
  },
] as const

