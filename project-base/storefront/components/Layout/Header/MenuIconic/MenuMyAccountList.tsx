import { MenuIconicSubItemLink } from './MenuIconicElements';
import { SalesRepresentative } from './SalesRepresentative';
import { EditIcon } from 'components/Basic/Icon/EditIcon';
import { ExitIcon } from 'components/Basic/Icon/ExitIcon';
import { SearchListIcon } from 'components/Basic/Icon/SearchListIcon';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TIDs } from 'cypress/tids';
import useTranslation from 'next-translate/useTranslation';
import { useCurrentCustomerUserPermissions } from 'utils/auth/useCurrentCustomerUserPermissions';
import { useLogout } from 'utils/auth/useLogout';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';

export const MenuMyAccountList: FC = () => {
    const { t } = useTranslation();
    const logout = useLogout();
    const { canManageUsers } = useCurrentCustomerUserPermissions();
    const { url } = useDomainConfig();
    const [customerOrdersUrl, customerComplaintsUrl, customerEditProfileUrl, customerUsersUrl] =
        getInternationalizedStaticUrls(
            ['/customer/orders', '/customer/complaints', '/customer/edit-profile', '/customer/users'],
            url,
        );

    const userMenuItemTwClass =
        'h-14 rounded-xl bg-backgroundAccentLess border border-background hover:bg-background hover:border-borderAccentLess';
    const userMenuItemIconTwClass = 'flex flex-row w-[44px] justify-start';

    return (
        <ul className="flex flex-col max-h-[87dvh] overflow-auto gap-2 p-1">
            <li className={userMenuItemTwClass}>
                <MenuIconicSubItemLink href={customerOrdersUrl} tid={TIDs.header_my_orders_link} type="orderList">
                    <div className={userMenuItemIconTwClass}>
                        <SearchListIcon className="w-6 h-6" />
                    </div>
                    {t('My orders')}
                </MenuIconicSubItemLink>
            </li>
            <li className={userMenuItemTwClass}>
                <MenuIconicSubItemLink
                    href={customerComplaintsUrl}
                    tid={TIDs.header_my_complaints_link}
                    type="complaintList"
                >
                    <div className={userMenuItemIconTwClass}>
                        <SearchListIcon className="w-6 h-6" />
                    </div>
                    {t('My complaints')}
                </MenuIconicSubItemLink>
            </li>
            <li className={userMenuItemTwClass}>
                <MenuIconicSubItemLink
                    href={customerEditProfileUrl}
                    tid={TIDs.header_edit_profile_link}
                    type="editProfile"
                >
                    <div className={userMenuItemIconTwClass}>
                        <EditIcon className="w-6 h-6" />
                    </div>
                    {t('Edit profile')}
                </MenuIconicSubItemLink>
            </li>
            {canManageUsers && (
                <li className={userMenuItemTwClass}>
                    <MenuIconicSubItemLink href={customerUsersUrl} type="customer-users">
                        <div className={userMenuItemIconTwClass}>
                            <UserIcon className="w-6 max-h-[22px]" />
                        </div>
                        {t('Customer users')}
                    </MenuIconicSubItemLink>
                </li>
            )}
            <li className={userMenuItemTwClass}>
                <MenuIconicSubItemLink tid={TIDs.header_logout} onClick={logout}>
                    <div className={userMenuItemIconTwClass}>
                        <ExitIcon className="w-6 h-6" />
                    </div>
                    {t('Logout')}
                </MenuIconicSubItemLink>
            </li>
            <SalesRepresentative />
        </ul>
    );
};
