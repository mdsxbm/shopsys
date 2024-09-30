import { MenuIconicItemLink, MenuIconicSubItemLink } from './MenuIconicElements';
import { SalesRepresentative } from './SalesRepresentative';
import { EditIcon } from 'components/Basic/Icon/EditIcon';
import { ExitIcon } from 'components/Basic/Icon/ExitIcon';
import { RemoveIcon } from 'components/Basic/Icon/RemoveIcon';
import { SearchListIcon } from 'components/Basic/Icon/SearchListIcon';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { Overlay } from 'components/Basic/Overlay/Overlay';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TIDs } from 'cypress/tids';
import useTranslation from 'next-translate/useTranslation';
import { useState } from 'react';
import { twJoin } from 'tailwind-merge';
import { useCurrentCustomerUserPermissions } from 'utils/auth/useCurrentCustomerUserPermissions';
import { useLogout } from 'utils/auth/useLogout';
import { desktopFirstSizes } from 'utils/mediaQueries';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';
import { twMergeCustom } from 'utils/twMerge';
import { useGetWindowSize } from 'utils/ui/useGetWindowSize';
import { useDebounce } from 'utils/useDebounce';

export const MenuIconicItemUserAuthenticated: FC = () => {
    const { t } = useTranslation();
    const logout = useLogout();
    const { url } = useDomainConfig();
    const { canManageUsers } = useCurrentCustomerUserPermissions();
    const [customerUrl, customerOrdersUrl, customerComplaintsUrl, customerEditProfileUrl, customerUsersUrl] =
        getInternationalizedStaticUrls(
            ['/customer', '/customer/orders', '/customer/complaints', '/customer/edit-profile', '/customer/users'],
            url,
        );
    const [isClicked, setIsClicked] = useState(false);
    const [isHovered, setIsHovered] = useState(false);
    const isHoveredDelayed = useDebounce(isHovered, 200);

    const userMenuItemTwClass =
        'h-14 rounded-xl bg-backgroundAccentLess border border-background hover:bg-background hover:border-borderAccentLess';
    const userMenuItemIconTwClass = 'flex flex-row w-[44px] justify-start';

    const { width } = useGetWindowSize();
    const isDesktop = width > desktopFirstSizes.tablet;

    return (
        <>
            <div
                className={twJoin('group lg:relative', (isClicked || isHovered) && 'z-aboveOverlay')}
                tid={TIDs.my_account_link}
                onMouseEnter={() => isDesktop && setIsHovered(true)}
                onMouseLeave={() => isDesktop && setIsHovered(false)}
            >
                <MenuIconicItemLink
                    className="rounded-t p-3 max-lg:hidden transition-all"
                    href={customerUrl}
                    type="account"
                >
                    <div className="relative">
                        <UserIcon className="w-6 max-h-[22px]" />
                        <div className="w-[10px] h-[10px] absolute -right-1 -top-1 rounded-full bg-actionPrimaryBackground" />
                    </div>
                    {t('My account')}
                </MenuIconicItemLink>

                <div className="order-2 flex h-full w-12 cursor-pointer items-center justify-center text-lg outline-none lg:hidden">
                    <div
                        onClick={() => {
                            setIsClicked(!isClicked);
                            setIsClicked(!isHovered);
                        }}
                    >
                        <div className="relative flex h-full w-full items-center justify-center text-textInverted transition-colors">
                            <UserIcon className="w-6 text-textInverted max-h-[22px]" />
                            <div className="w-[10px] h-[10px] absolute -right-1 -top-1 rounded-full bg-actionPrimaryBackground" />
                        </div>
                    </div>
                </div>

                <div
                    className={twMergeCustom(
                        'pointer-events-none absolute top-0 -right-[100%] block w-[335px] lg:w-[315px] origin-top-right rounded-xl px-5',
                        'lg:top-full lg:transition-all lg:p-5',
                        'bg-none scale-50 opacity-0',
                        isHoveredDelayed &&
                            'group-hover:bg-background group-hover:scale-100 group-hover:opacity-100 group-hover:pointer-events-auto z-cart',
                        isClicked &&
                            'scale-100 opacity-100 bg-background top-0 right-0 rounded-none h-dvh fixed z-aboveOverlay pointer-events-auto transition-[right]',
                    )}
                >
                    <div className="flex flex-row justify-between m-5 lg:hidden">
                        <span className="text-base w-full text-center">{t('My account')}</span>
                        <RemoveIcon
                            className="w-4 text-borderAccent cursor-pointer"
                            onClick={() => setIsClicked(false)}
                        />
                    </div>
                    <ul className="flex flex-col max-h-[87dvh] overflow-auto gap-2 p-1">
                        <li className={userMenuItemTwClass}>
                            <MenuIconicSubItemLink
                                href={customerOrdersUrl}
                                tid={TIDs.header_my_orders_link}
                                type="orderList"
                            >
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
                </div>
            </div>

            <Overlay
                isActive={isClicked || isHoveredDelayed}
                onClick={() => {
                    setIsClicked(false);
                    setIsHovered(false);
                }}
            />
        </>
    );
};
