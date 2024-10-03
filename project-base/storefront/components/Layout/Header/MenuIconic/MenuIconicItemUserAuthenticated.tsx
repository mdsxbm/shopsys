import { MenuIconicItemLink } from './MenuIconicElements';
import { MenuIconicItemUserAuthenticatedPopover } from './MenuIconicItemUserAuthenticatedPopover';
import { MenuMyAccountList } from './MenuMyAccountList';
import { Drawer } from 'components/Basic/Drawer/Drawer';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { Overlay } from 'components/Basic/Overlay/Overlay';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TIDs } from 'cypress/tids';
import useTranslation from 'next-translate/useTranslation';
import { useState } from 'react';
import { desktopFirstSizes } from 'utils/mediaQueries';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';
import { twMergeCustom } from 'utils/twMerge';
import { useGetWindowSize } from 'utils/ui/useGetWindowSize';
import { useDebounce } from 'utils/useDebounce';

export const MenuIconicItemUserAuthenticated: FC = () => {
    const { t } = useTranslation();
    const { url } = useDomainConfig();
    const [customerUrl] = getInternationalizedStaticUrls(['/customer'], url);
    const [isClicked, setIsClicked] = useState(false);
    const [isHovered, setIsHovered] = useState(true);
    const isHoveredDelayed = useDebounce(isHovered, 200);

    const { width } = useGetWindowSize();
    const isDesktop = width > desktopFirstSizes.tablet;

    return (
        <>
            <div
                className={twMergeCustom('group lg:relative lg:flex', (isClicked || isHovered) && 'z-aboveOverlay')}
                tid={TIDs.my_account_link}
                onMouseEnter={() => isDesktop && setIsHovered(true)}
                onMouseLeave={() => isDesktop && setIsHovered(true)}
            >
                <MenuIconicItemLink
                    className="rounded-t p-3 max-lg:hidden transition-all"
                    href={customerUrl}
                    type="account"
                >
                    <div className="relative">
                        <UserIcon className="w-6 max-h-5.5" />
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
                            <UserIcon className="w-6 text-textInverted max-h-5.5" />
                            <div className="w-[10px] h-[10px] absolute -right-1 -top-1 rounded-full bg-actionPrimaryBackground" />
                        </div>
                    </div>
                </div>

                <Drawer className="lg:hidden" isClicked={isClicked} setIsClicked={setIsClicked} title={t('My account')}>
                    <MenuMyAccountList />
                </Drawer>

                <MenuIconicItemUserAuthenticatedPopover isHovered={isHoveredDelayed}>
                    <MenuMyAccountList />
                </MenuIconicItemUserAuthenticatedPopover>
            </div>

            <Overlay
                isActive={isClicked || isHoveredDelayed}
                onClick={() => {
                    setIsClicked(false);
                    setIsHovered(true);
                }}
            />
        </>
    );
};
