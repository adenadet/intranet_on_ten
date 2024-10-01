import {createRouter, createWebHistory} from 'vue-router';

import DashboardMain        from '../dashboard/Main.vue';

import EServiceAdminDashboard        from '../eservices/admin/Dashboard.vue';
import EServiceAdminReportDetailed   from '../eservices/admin/Detailed.vue';
import EServiceAdminReportHomeOffice from '../eservices/admin/HomeOffice.vue';
import EServiceAdminReportSummary    from '../eservices/admin/Summary.vue';

import EServiceCertificate           from '../eservices/certificates/Certificate.vue';
import EServiceCertificateBioData    from '../eservices/certificates/BioData.vue';
import EServiceCertificateFooter     from '../eservices/certificates/Footer.vue';
import EServiceCertificateHeader     from '../eservices/certificates/Header.vue';
import EServiceCertificateSummary    from '../eservices/certificates/Summary.vue';
import EServiceCertificateSummaryKid from '../eservices/certificates/SummaryKid.vue';
import EServiceCertificateSummaryLab from '../eservices/certificates/SummaryLab.vue';

import EServiceFrontAppointment      from '../eservices/front/Appointment.vue';
import EServiceFrontAppointments     from '../eservices/front/Appointments.vue';
import EServiceFrontCertificates     from '../eservices/front/Certificates.vue';
import EServiceFrontMissed           from '../eservices/front/Missed.vue';
import EServiceFrontPatients         from '../eservices/front/Patients.vue';
import EServicePayments              from '../eservices/front/Payments.vue';
import EServicePayment               from '../eservices/front_admin/Payment.vue';
import EServiceRadiographer          from '../eservices/front/Radiographer.vue';
import EServiceFrontReferral         from '../eservices/front/Referral.vue';

import EServiceFrontAdminPatients       from '../eservices/front_admin/Patients.vue';
import EServiceFrontAdminAppointment    from '../eservices/front_admin/Appointment.vue';
import EServiceFrontAdminAppointments   from '../eservices/front_admin/Appointments.vue';
import EServiceFrontAdminPayments       from '../eservices/front_admin/Payments.vue';

import EServiceDocConsultation       from '../eservices/doctor/Consultation.vue';
import EServiceDocConsultations      from '../eservices/doctor/Consultations.vue';
import EServiceDocConsultationSingle from '../eservices/doctor/ConsultationSingle.vue';
import EServiceDocConsentView        from '../eservices/doctor/ConsentView.vue';
import EServiceDocConsultationView   from '../eservices/doctor/ConsultationView.vue';
import EServiceDocPending            from '../eservices/doctor/Pending.vue';
import EServiceDocLaboratoryView     from '../eservices/doctor/LaboratoryView.vue';
import EServiceDocPatientView        from '../eservices/doctor/PatientView.vue';
import EServiceDocReportView         from '../eservices/doctor/ReportView.vue';
import EServiceDocReviews            from '../eservices/doctor/Reviews.vue';
import EServiceDocView               from '../eservices/doctor/View.vue';

import EServiceRadReport             from '../eservices/radiologist/Report.vue';
import EServiceRadReports            from '../eservices/radiologist/Reports.vue';
import EServiceRadReviews            from '../eservices/radiologist/Reviews.vue';

    import EServiceDetailAppointment        from '../eservices/details/Appointment.vue';
    import EServiceDetailAppointmentList    from '../eservices/details/AppointmentList.vue';
    import EServiceDetailIssueView          from '../eservices/details/IssueView.vue';
    import EServiceDetailPaymentList        from '../eservices/details/PaymentList.vue';
    import EServiceDetailReferral           from '../eservices/details/Referral.vue';
    import EServiceDetailReport             from '../eservices/details/Report.vue';

    import EServiceFormAppointment      from '../eservices/forms/Appointment.vue';
    import EServiceFormArrival          from '../eservices/forms/Arrival.vue';
    import EServiceFormPatient          from '../eservices/forms/Patient.vue';
    import EServiceFormPayment          from '../eservices/forms/Payment.vue';
    import EServiceFormReport           from '../eservices/forms/Report.vue';
    import EServiceFormSearch           from '../eservices/forms/Search.vue';

    import EServiceDocFormConsent              from '../eservices/doctor/forms/Consent.vue';
    import EServiceDocFormConsentPad           from '../eservices/doctor/forms/ConsentPad.vue';
    import EServiceDocFormIssue                from '../eservices/doctor/forms/Issue.vue';
    import EServiceDocFormLaboratory           from '../eservices/doctor/forms/Laboratory.vue';
    import EServiceDocFormReferral             from '../eservices/doctor/forms/Referral.vue';
    import EServiceDocFormScreening            from '../eservices/doctor/forms/Screening.vue';

import ExternalDone                         from '../external/Done.vue';
    import ExternalDetailPolicy                 from '../external/details/Policy.vue';
    import ExternalFormDirect                   from '../external/forms/Direct.vue';
    import ExternalFormReschedule               from '../external/forms/Reschedule.vue';

import HrmsEmployee                     from '../hrms/Employee.vue';
import HrmsEmployees                    from '../hrms/Employees.vue';
import HrmsLeaveRequest                 from '../hrms/leaves/Request.vue';
import HrmsLeaveRequestAdmin            from '../hrms/leaves/RequestAdmin.vue';
import HrmsLeaveRequests                from '../hrms/leaves/Requests.vue';
import HrmsLeaveType                    from '../hrms/leaves/Type.vue';
import HrmsLeaveTypes                   from '../hrms/leaves/Types.vue';
    
    import HrmsDetailAssignedEmployeeLeaveType  from '../hrms/details/AssignedEmployeeLeaveType.vue';     
    import HrmsDetailEmployee                   from '../hrms/details/Employee.vue';
    import HrmsDetailLeaveRequest               from '../hrms/details/LeaveRequest.vue';
    import HrmsDetailLeaveRequestList           from '../hrms/details/LeaveRequestList.vue';       

    import HrmsFormLeaveRequest                 from '../hrms/forms/LeaveRequest.vue';
    import HrmsFormLeaveType                    from '../hrms/forms/LeaveType.vue';

import NoticeAdmin                  from '../notices/Admin.vue';
import NoticeAll                    from '../notices/All.vue';
import NoticeBoard                  from '../notices/Board.vue';
import NoticeSingle                 from '../notices/Single.vue';
        
    import NoticeDetailList             from '../notices/details/List.vue';    

    import NoticeForm    from '../notices/forms/New.vue';

import SOMAdmin             from '../som/Admin.vue';
import SOMCloseNominations  from '../som/CloseNominations.vue';
import SOMDetail            from '../som/Detail.vue';
/*import SOMCloseVoting       from '../som/CloseVoting.vue';
import SOMCloseWinners      from '../som/CloseWinners.vue';

import SOMNominate          from '../som/Nominate.vue'; 
import SOMOpen              from '../som/Open.vue'; 
import SOMView              from '../som/View.vue';
import SOMVote              from '../som/Vote.vue';*/
import SOMWinners           from '../som/Winners.vue';

    import SOMDetailNominations     from '../som/details/Nominations.vue';  
    import SOMDetailVotes           from '../som/details/Votes.vue';    
    import SOMDetailWinners         from '../som/details/Winners.vue';
    import SOMFormMonth             from '../som/forms/Month.vue';


import TicketAdmin              from '../ticketing/Admin.vue';

import TicketDepartment         from '../ticketing/Department.vue';
import TicketPersonal           from '../ticketing/Personal.vue';
import TicketSetting            from '../ticketing/Setting.vue';
import TicketSingle             from '../ticketing/Single.vue';

    import TicketDetailDashboard        from '../ticketing/details/Dashboard.vue';
    import TicketDetailList             from '../ticketing/details/List.vue';

    import TicketFormAssign     from '../ticketing/forms/Assign.vue';
    import TicketFormAccept     from '../ticketing/forms/Accept.vue';
    import TicketFormComplete   from '../ticketing/forms/Complete.vue';
    import TicketFormNew        from '../ticketing/forms/New.vue';
    import TicketFormReply      from '../ticketing/forms/Reply.vue';

import UserBirthday         from '../users/Birthday.vue';
import UserBirthdays        from '../users/Birthdays.vue';
import UserContacts         from '../users/Contacts.vue';
import UserContact          from '../users/Contact.vue';
import UserProfile          from '../users/Profile.vue';
import UserRole             from '../users/Role.vue';
import UserRoles            from '../users/Roles.vue';
import UserStaffs           from '../users/Staffs.vue';
import UserStaffsLatest     from '../users/StaffsLatest.vue';

    import UserDetailBioData        from '../users/details/BioData.vue';
    import UserFormAssignRole       from '../users/forms/AssignRole.vue';
    import UserFormBioData          from '../users/forms/BioData.vue';
    import UserFormNOK              from '../users/forms/NextOfKin.vue';
    import UserFormPassword         from '../users/forms/Password.vue';
    import UserFormRole             from '../users/forms/Role.vue';
    import UserFormStaff            from '../users/forms/Staff.vue';    

import Error404 from '../general/errors/404.vue';
import component from 'vue3-paystack';

const routes = [
    {path: '/',             component: DashboardMain},
    {path: '/contacts',     component: UserContacts},
    {path: '/contacts/:id', component: UserContact},
    {path: '/dashboard',    component: DashboardMain},
    
    //EServices
    {path: '/eservices/administrator',                      component:EServiceAdminDashboard},
    {path: '/eservices/administrator/dashboard',            component:EServiceAdminDashboard},
    {path: '/eservices/administrator/detailed_reports',     component:EServiceAdminReportDetailed},
    {path: '/eservices/administrator/home_office_reports',  component:EServiceAdminReportHomeOffice},
    {path: '/eservices/administrator/summary_reports',      component:EServiceAdminReportSummary},
    {path: '/eservices/administrator/dashboard',            component:EServiceAdminDashboard},

    {path: '/eservices/certificate/:id',                    component:EServiceCertificate},
    {path: '/eservices/doctor',                             component:EServiceDocConsultations},
    {path: '/eservices/doctor/consultations',               component:EServiceDocConsultations},
    {path: '/eservices/doctor/consultations/:id',           component:EServiceDocConsultationSingle},
    {path: '/eservices/doctor/consultation/:id',            component:EServiceDocConsultation},
    {path: '/eservices/doctor/pending',                     component:EServiceDocPending},
    {path: '/eservices/doctor/reviews',                     component:EServiceDocReviews},
    
    {path: '/eservices/front_admin',                        component:EServiceFrontAdminAppointments},
    {path: '/eservices/front_admin/applicants',             component:EServiceFrontAdminPatients},
    {path: '/eservices/front_admin/appointments',           component:EServiceFrontAdminAppointments},
    {path: '/eservices/front_admin/appointment/:id',        component:EServiceFrontAdminAppointment},
    {path: '/eservices/front_admin/appointments/missed',    component:EServiceFrontMissed},
    {path: '/eservices/front_admin/certificates',           component:EServiceFrontCertificates},
    {path: '/eservices/front_admin/payments',               component:EServiceFrontAdminPayments},

    
    {path: '/eservices/front_office',                       component:EServiceFrontAppointments},
    {path: '/eservices/front_office/appointments',          component:EServiceFrontAppointments},
    {path: '/eservices/front_office/appointment/:id',       component:EServiceFrontAppointment},
    {path: '/eservices/front_office/payments',              component:EServicePayments},
    {path: '/eservices/front_office/radiographer',          component:EServiceRadiographer},
    {path: '/eservices/front_office/referral/:id',          component:EServiceFrontReferral}, 
    {path: '/eservices/front_office/Certificates',          component:EServiceFrontCertificates},

    {path: '/eservices/radiologist',                        component:EServiceRadReports},
    {path: '/eservices/radiologist/reports',                component:EServiceRadReports},
    {path: '/eservices/radiologist/reviews',                component:EServiceRadReviews},
    {path: '/eservices/radiologist/report/:id',             component:EServiceRadReport},

    {path: '/home',                                         component: DashboardMain},
    
    //HRMS
    {path: '/hrms/admin/employees',                         component: HrmsEmployees},
    {path: '/hrms/admin/employees/:id',                     component: HrmsEmployee},
    {path: '/hrms/admin/leaves/requests',                   component: HrmsLeaveRequestAdmin},
    {path: '/hrms/admin/leaves/types',                      component: HrmsLeaveTypes},
    {path: '/hrms/admin/leaves/types/:id',                  component: HrmsLeaveType},

    {path: '/hrms/leaves/all_requests',                     component: HrmsLeaveRequestAdmin},
    {path: '/hrms/leaves/requests',                         component: HrmsLeaveRequests},
    {path: '/hrms/leaves/types',                            component: HrmsLeaveTypes},
    
    {path:'/profile',                                       component: UserProfile},

    //Notices
    {path: '/notices',                                      component: NoticeAll},
    {path: '/notices/admin',                                component: NoticeAdmin},
    {path: '/notices/:id',                                  component: NoticeSingle},

    //Staff of the Month
    {path: '/staff_month',                                  component: SOMWinners},
    {path: '/staff_month/admin',                            component: SOMAdmin},
    /*{path: '/staff_month/open',             component: SOMOpen},
    {path: '/staff_month/nominate',         component: SOMNominate},
    //{path: '/staff_month/view/:month',    component: SocialAlbum},
    {path: '/staff_month/vote',             component: SOMVote},*/
    {path: '/staff_month/winners',          component: SOMWinners},

    //Ticketing
    {path: '/ticketing',                                    component: TicketPersonal},
    {path: '/ticketing/admin',                              component: TicketAdmin},
    {path: '/ticketing/department',                         component: TicketDepartment},
    {path: '/ticketing/settings',                           component: TicketSetting},
    {path: '/ticketing/:id',                                component: TicketSingle},

    {path: '/uk-tb-reschedules',                            component: ExternalFormReschedule},
    {path: '/uk-tb-screening',                              component: ExternalFormDirect},
    {path: '/users',                                        component: UserStaffs},
    {path: '/:pathMatch(.*)*',                              component: Error404},
];
const router = createRouter({
    history: createWebHistory(),
    routes
});

export function registerGlobalComponents(app) {
    app.component('DashboardMain',           DashboardMain);

    app.component('EServiceCertificate',             EServiceCertificate);
    app.component('EServiceCertificateBioData',      EServiceCertificateBioData);
    app.component('EServiceCertificateHeader',       EServiceCertificateHeader);
    app.component('EServiceCertificateFooter',       EServiceCertificateFooter);
    app.component('EServiceCertificateSummary',      EServiceCertificateSummary);
    app.component('EServiceCertificateSummaryKid',   EServiceCertificateSummaryKid);
    app.component('EServiceCertificateSummaryLab',   EServiceCertificateSummaryLab);

    app.component('EServiceFrontAppointment',        EServiceFrontAppointment);
    app.component('EServiceFrontAppointments',       EServiceFrontAppointments);
    app.component('EServiceFrontCertificates',       EServiceFrontCertificates);
    app.component('EServiceFrontMissed',             EServiceFrontMissed);
    app.component('EServiceFrontPatients',           EServiceFrontPatients);
    app.component('EServiceFrontReferral',           EServiceFrontReferral);
    app.component('EServicePayments',                EServicePayments);
    app.component('EServicePayment',                 EServicePayment);

    app.component('EServiceFrontAdminAppointment',   EServiceFrontAdminAppointment);
    app.component('EServiceFrontAdminAppointments',  EServiceFrontAdminAppointments);
    app.component('EServiceFrontAdminPatients',      EServiceFrontAdminPatients);

    app.component('EServiceDocConsultation',         EServiceDocConsultation);
    app.component('EServiceDocConsultations',        EServiceDocConsultations);
    app.component('EServiceDocConsultationSingle',   EServiceDocConsultationSingle);
    app.component('EServiceDocConsentView',          EServiceDocConsentView);
    app.component('EServiceDocConsultationView',     EServiceDocConsultationView);
    app.component('EServiceDocPending',              EServiceDocPending); 
    app.component('EServiceDocLaboratoryView',       EServiceDocLaboratoryView); 
    app.component('EServiceDocPatientView',          EServiceDocPatientView); 
    app.component('EServiceDocReportView',           EServiceDocReportView); 
    app.component('EServiceDocReviews',              EServiceDocReviews);
    app.component('EServiceDocView',                 EServiceDocView);

    app.component('EServiceRadReport',                      EServiceRadReport);
    app.component('EServiceRadReports',                     EServiceRadReports);
    app.component('EServiceRadReviews',                     EServiceRadReviews);

        app.component('EServiceDetailAppointmentList',      EServiceDetailAppointmentList);  
        app.component('EServiceDetailAppointment',          EServiceDetailAppointment);
        app.component('EServiceDetailIssueView',            EServiceDetailIssueView);
        app.component('EServiceDetailPaymentList',          EServiceDetailPaymentList);
        app.component('EServiceDetailReferral',             EServiceDetailReferral);  
        app.component('EServiceDetailReport',               EServiceDetailReport);  

        app.component('EServiceFormAppointment',            EServiceFormAppointment);
        app.component('EServiceFormArrival',                EServiceFormArrival);
        app.component('EServiceFormPatient',                EServiceFormPatient);
        app.component('EServiceFormPayment',                EServiceFormPayment);
        app.component('EServiceFormReport',                 EServiceFormReport);
        app.component('EServiceFormSearch',                 EServiceFormSearch);

        app.component('EServiceDocFormConsent',             EServiceDocFormConsent);
        app.component('EServiceDocFormConsentPad',          EServiceDocFormConsentPad);
        app.component('EServiceDocFormIssue',               EServiceDocFormIssue);
        app.component('EServiceDocFormLaboratory',          EServiceDocFormLaboratory);
        app.component('EServiceDocFormReferral',            EServiceDocFormReferral);
        app.component('EServiceDocFormScreening',           EServiceDocFormScreening);

    app.component('ExternalDone',               ExternalDone);
        app.component('ExternalDetailPolicy',           ExternalDetailPolicy);
        app.component('ExternalFormDirect',             ExternalFormDirect);
        app.component('ExternalFormReschedule',         ExternalFormReschedule);

    app.component('HrmsEmployee',               HrmsEmployee);
    app.component('HrmsEmployees',              HrmsEmployees);
    app.component('HrmsLeaveRequest',           HrmsLeaveRequest);
    app.component('HrmsLeaveRequestAdmin',      HrmsLeaveRequestAdmin);
    app.component('HrmsLeaveRequests',          HrmsLeaveRequests);
    app.component('HrmsLeaveType',              HrmsLeaveType);
    app.component('HrmsLeaveTypes',             HrmsLeaveTypes);
        app.component('HrmsDetailAssignedEmployeeLeaveType', HrmsDetailAssignedEmployeeLeaveType);
        app.component('HrmsDetailEmployee',                 HrmsDetailEmployee);
        app.component('HrmsDetailLeaveRequest',             HrmsDetailLeaveRequest);
        app.component('HrmsDetailLeaveRequestList',         HrmsDetailLeaveRequestList);

        app.component('HrmsFormLeaveRequest',               HrmsFormLeaveRequest);
        app.component('HrmsFormLeaveType',                  HrmsFormLeaveType);


    app.component('NoticeAll',          NoticeAll);
    app.component('NoticeAdmin',        NoticeAdmin);
    app.component('NoticeBoard',        NoticeBoard);
    app.component('NoticeSingle',       NoticeSingle);

        app.component('NoticeDetailList',           NoticeDetailList);
        
        app.component('NoticeForm',                 NoticeForm);        

    app.component('SOMAdmin',               SOMAdmin);
    app.component('SOMCloseNominations',    SOMCloseNominations);
    app.component('SOMDetail',              SOMDetail);
    /*app.component('SOMCloseVoting',         SOMCloseVoting);
    app.component('SOMCloseWinners',        SOMCloseWinners);
    
    app.component('SOMNominate',            SOMNominate);
    app.component('SOMView',                SOMView);
    app.component('SOMVote',                SOMVote);*/
        app.component('SOMDetailNominations',       SOMDetailNominations);
        app.component('SOMDetailVotes',             SOMDetailVotes);
        app.component('SOMDetailWinners',           SOMDetailWinners);
        app.component('SOMFormMonth', SOMFormMonth);
        
    
    app.component('TicketAdmin',            TicketAdmin);
    app.component('TicketDepartment',       TicketDepartment);
    app.component('TicketPersonal',         TicketPersonal);
    app.component('TicketSingle',           TicketSingle);

        app.component('TicketDetailDashboard',      TicketDetailDashboard);
        app.component('TicketDetailList',           TicketDetailList);

        app.component('TicketFormAccept',   TicketFormAccept);
        app.component('TicketFormAssign',   TicketFormAssign);
        app.component('TicketFormComplete', TicketFormComplete);
        app.component('TicketFormNew',      TicketFormNew);
        app.component('TicketFormReply',    TicketFormReply);
    
    app.component('UserBirthday',            UserBirthday);
    app.component('UserBirthdays',           UserBirthdays);
    app.component('UserContact',             UserContact);
    app.component('UserContacts',            UserContacts);
    app.component('UserProfile',             UserProfile);
    app.component('UserRole',                UserRole);
    app.component('UserRoles',               UserRoles);
    app.component('UserStaffs',              UserStaffs);
    app.component('UserStaffsLatest',        UserStaffsLatest);

        app.component('UserDetailBioData',          UserDetailBioData);
        app.component('UserFormAssignRole',         UserFormAssignRole);
        app.component('UserFormBioData',            UserFormBioData);
        app.component('UserFormNOK',                UserFormNOK);
        app.component('UserFormPassword',           UserFormPassword);
        app.component('UserFormRole',               UserFormRole);
        app.component('UserFormStaff',              UserFormStaff);
}

export default router;