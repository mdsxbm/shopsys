import { MenuIconicSubItemLink } from './MenuIconicElements';
import { SalesRepresentative } from './SalesRepresentative';
import { EditIcon } from 'components/Basic/Icon/EditIcon';
import { ExitIcon } from 'components/Basic/Icon/ExitIcon';
import { SearchListIcon } from 'components/Basic/Icon/SearchListIcon';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TIDs } from 'cypress/tids';
import useTranslation from 'next-translate/useTranslation';
import { twJoin } from 'tailwind-merge';
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

    return (
        <ul className="flex flex-col max-h-[87dvh] overflow-auto gap-2 p-1">
            <MenuMyAccountListItem>
                <MenuIconicSubItemLink href={customerOrdersUrl} tid={TIDs.header_my_orders_link} type="orderList">
                    <SearchListIcon className="size-6 mr-5" />
                    {t('My orders')}
                </MenuIconicSubItemLink>
            </MenuMyAccountListItem>
            <MenuMyAccountListItem>
                <MenuIconicSubItemLink
                    href={customerComplaintsUrl}
                    tid={TIDs.header_my_complaints_link}
                    type="complaintList"
                >
                    <SearchListIcon className="size-6 mr-5" />
                    {t('My complaints')}
                </MenuIconicSubItemLink>
            </MenuMyAccountListItem>
            <MenuMyAccountListItem>
                <MenuIconicSubItemLink
                    href={customerEditProfileUrl}
                    tid={TIDs.header_edit_profile_link}
                    type="editProfile"
                >
                    <EditIcon className="size-6 mr-5" />
                    {t('Edit profile')}
                </MenuIconicSubItemLink>
            </MenuMyAccountListItem>
            {canManageUsers && (
                <MenuMyAccountListItem>
                    <MenuIconicSubItemLink href={customerUsersUrl} type="customer-users">
                        <UserIcon className="w-6 mr-5 max-h-5.5" />
                        {t('Customer users')}
                    </MenuIconicSubItemLink>
                </MenuMyAccountListItem>
            )}
            <MenuMyAccountListItem>
                <MenuIconicSubItemLink tid={TIDs.header_logout} onClick={logout}>
                    <ExitIcon className="size-6 mr-5" />
                    {t('Logout')}
                </MenuIconicSubItemLink>
            </MenuMyAccountListItem>
            <SalesRepresentative />
        </ul>
    );
};

const MenuMyAccountListItem: FC = ({ children }) => (
    <li
        className={twJoin(
            'h-14 rounded-xl bg-backgroundAccentLess border border-background',
            'hover:bg-background hover:border-borderAccentLess',
        )}
    >
        {children}
    </li>
);
