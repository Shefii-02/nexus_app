import { Navigate, Route, Routes } from 'react-router-dom'
import ProtectedRoute from '../layouts/ProtectedRoute'
import { Outlet } from 'react-router-dom'
import ProtectedLayout from '../layouts/ProtectedLayout'
import DashboardLayout from '../layouts/DashboardLayout'
import DashboardPage from '../modules/dashboard/DashboardPage'
import LoginPage from '../modules/auth/pages/LoginPage'
import TeacherListPage from '../modules/teachers/pages/TeacherListPage'
import CreateTeacherPage from '../modules/teachers/pages/CreateTeacherPage'
import TeacherViewPage from '../modules/teachers/pages/TeacherViewPage'
import EditTeacherPage from '../modules/teachers/pages/EditTeacherPage'
import StudentListPage from '../modules/students/pages/StudentListPage'
import CreateStudentPage from '../modules/students/pages/CreateStudentPage'
import EditStudentPage from '../modules/students/pages/EditStudentPage'
import StudentViewPage from '../modules/students/pages/StudentViewPage'
import StaffListPage from '../modules/staff/pages/StaffListPage'
import CreateStaffPage from '../modules/staff/pages/CreateStaffPage'
import EditStaffPage from '../modules/staff/pages/EditStaffPage'
import StaffViewPage from '../modules/staff/pages/StaffViewPage'
import CourseListPage from '../modules/courses/pages/CourseListPage'
import CreateCoursePage from '../modules/courses/pages/CreateCoursePage'
import EditCoursePage from '../modules/courses/pages/EditCoursePage'
import CourseClassListPage from '../modules/courseClasses/pages/CourseClassListPage'
import CreateCourseClassPage from '../modules/courseClasses/pages/CreateCourseClassPage'
import EditCourseClassPage from '../modules/courseClasses/pages/EditCourseClassPage'
import ClassLinksPage from '../modules/courseClasses/pages/ClassLinksPage'
import ClassMaterialsPage from '../modules/courseMaterials/ClassMaterialsPage'
import GroupListPage from '../modules/groups/pages/GroupListPage'
import CreateGroupPage from '../modules/groups/pages/CreateGroupPage'
import EditGroupPage from '../modules/groups/pages/EditGroupPage'
import PaymentListPage from '../modules/payments/pages/PaymentListPage'
import CreatePaymentPage from '../modules/payments/pages/CreatePaymentPage'
import EditPaymentPage from '../modules/payments/pages/EditPaymentPage'
import EnrollAdmissionPage from '../modules/payments/pages/EnrollAdmissionPage'
import AdmissionsListPage from '../modules/payments/pages/AdmissionsListPage'
import TransactionListPage from '../modules/payments/pages/TransactionListPage'
import RenewalPaymentsPage from '../modules/payments/pages/RenewalPaymentsPage'
import AnnouncementListPage from '../modules/announcements/pages/AnnouncementListPage'
import CreateAnnouncementPage from '../modules/announcements/pages/CreateAnnouncementPage'
import EditAnnouncementPage from '../modules/announcements/pages/EditAnnouncementPage'
import NotificationListPage from '../modules/notifications/pages/NotificationListPage'
import ViewNotificationPage from '../modules/notifications/pages/ViewNotificationPage'

import CreateNotificationPage from '../modules/notifications/pages/CreateNotificationPage'
import EditNotificationPage from '../modules/notifications/pages/EditNotificationPage'
import RoleListPage from '../modules/role/pages/RoleListPage'
import CreateRolePage from '../modules/role/pages/CreateRolePage'
import EditRolePage from '../modules/role/pages/EditRolePage'
import RoleViewPage from '../modules/role/pages/RoleViewPage'
import ViewCourseClassPage from '../modules/courseClasses/pages/ViewCourseClassPage'
import CreateMaterial from '../modules/courseMaterials/CreateMaterial'
import EditMaterial from '../modules/courseMaterials/EditMaterial'
import ViewMaterial from '../modules/courseMaterials/ViewMaterial'
import CourseViewPage from '../modules/courses/pages/CourseViewPage'
import ConversationPage from '../modules/conversations/ConversationPage'
import CreateAdmissionPage from '../modules/admissions/pages/CreateAdmissionPage'
import AdmissionListPage from '../modules/admissions/pages/AdmissionListPage'
import ViewAdmissionPage from '../modules/admissions/pages/ViewAdmissionPage'
import EditAdmissionPage from '../modules/admissions/pages/EditAdmissionPage'
import RenewalDuePage from '../modules/renewal/RenewalDuePage'
import RenewalHistoryPage from '../modules/renewal/RenewalHistoryPage'

const AppRoutes = () => (
  <Routes>
    <Route path="/login" element={<LoginPage />} />
    <Route element={<ProtectedRoute />}>
      <Route element={<ProtectedLayout><Outlet /></ProtectedLayout>}>
        {/* <Route element={<DashboardLayout><Outlet /></DashboardLayout>}> */}
        <Route path="/" element={<DashboardPage />} />
        <Route path="/teachers" element={<TeacherListPage />} />
        <Route path="/teachers/create" element={<CreateTeacherPage />} />
        <Route path="/teachers/:id" element={<TeacherViewPage />} />
        <Route path="/teachers/:id/edit" element={<EditTeacherPage />} />
        <Route path="/students" element={<StudentListPage />} />
        <Route path="/students/create" element={<CreateStudentPage />} />
        <Route path="/students/:id" element={<StudentViewPage />} />
        <Route path="/students/:id/edit" element={<EditStudentPage />} />
        <Route path="/staff" element={<StaffListPage />} />
        <Route path="/staff/create" element={<CreateStaffPage />} />
        <Route path="/staff/:id" element={<StaffViewPage />} />
        <Route path="/staff/:id/edit" element={<EditStaffPage />} />
        <Route path="/courses" element={<CourseListPage />} />
        <Route path="/courses/create" element={<CreateCoursePage />} />
        <Route path="/courses/:id/edit" element={<EditCoursePage />} />
        <Route path="/courses/:id" element={<CourseViewPage />} />

        <Route path="/courses/:courseId/classes" element={<CourseClassListPage />} />
        <Route path="/courses/:courseId/classes/create" element={<CreateCourseClassPage />} />
        <Route path="/courses/:courseId/classes/:classId/show" element={<ViewCourseClassPage />} />
        <Route path="/courses/:courseId/classes/:classId" element={<EditCourseClassPage />} />
        <Route path="/courses/:courseId/classes/links" element={<ClassLinksPage />} />

        <Route path="/courses/:courseId/materials" element={<ClassMaterialsPage />} />
        <Route path="/courses/:courseId/materials/create" element={<CreateMaterial />} />
        <Route path="/courses/:courseId/materials/:materialId" element={<ViewMaterial />} />
        <Route path="/courses/:courseId/materials/:materialId/edit" element={<EditMaterial />} />


        <Route path="/chats" element={<ConversationPage />} />
        <Route path="/chats/create" element={<CreateGroupPage />} />
        <Route path="/chats/:id/edit" element={<EditGroupPage />} />
        <Route path="/chats/:id" element={<EditGroupPage />} />

        {/*
        <Route path="/conversation/groups" element={<GroupListPage />} />
        <Route path="/conversation/groups/create" element={<CreateGroupPage />} />
        <Route path="/conversation/groups/:id/edit" element={<EditGroupPage />} />
        <Route path="/conversation/groups/:id" element={<EditGroupPage />} /> */}


        <Route path="/admissions/create" element={<CreateAdmissionPage />} />
        <Route path="/admissions" element={<AdmissionListPage />} />
        <Route path="/admissions/:id/show" element={<ViewAdmissionPage />} />
        <Route path="/admissions/:id/edit" element={<EditAdmissionPage />} />
        
        <Route path="/renewals/due" element={<RenewalDuePage />} />
        <Route path="/renewals" element={<RenewalHistoryPage />} />


        <Route path="/payments" element={<PaymentListPage />} />
        <Route path="/payments/create" element={<CreatePaymentPage />} />
        <Route path="/payments/:id/edit" element={<EditPaymentPage />} />

        <Route path="/payments/transactions" element={<TransactionListPage />} />
        <Route path="/announcements" element={<AnnouncementListPage />} />
        <Route path="/announcements/create" element={<CreateAnnouncementPage />} />
        <Route path="/announcements/:id/edit" element={<EditAnnouncementPage />} />

        <Route path="/notifications" element={<NotificationListPage />} />
        <Route path="/notifications/create" element={<CreateNotificationPage />} />
        <Route path="/notifications/:id" element={<ViewNotificationPage />} />
        <Route path="/notifications/:id/edit" element={<EditNotificationPage />} />

        <Route path="/roles" element={<RoleListPage />} />
        <Route path="/roles/create" element={<CreateRolePage />} />
        <Route path="/roles/:id" element={<RoleViewPage />} />
        <Route path="/roles/:id/edit" element={<EditRolePage />} />
      </Route>
    </Route>
    <Route path="*" element={<Navigate replace to="/" />} />
  </Routes>
)

export default AppRoutes
