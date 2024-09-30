import { MenuIconicItemLink } from './MenuIconicElements';
import { ExtendedNextLink } from 'components/Basic/ExtendedNextLink/ExtendedNextLink';
import { CheckmarkIcon } from 'components/Basic/Icon/CheckmarkIcon';
import { RemoveIcon } from 'components/Basic/Icon/RemoveIcon';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { Overlay } from 'components/Basic/Overlay/Overlay';
import { LoginForm } from 'components/Blocks/Login/LoginForm';
import { Button } from 'components/Forms/Button/Button';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { TIDs } from 'cypress/tids';
import useTranslation from 'next-translate/useTranslation';
import { useState, MouseEvent as ReactMouseEvent } from 'react';
import { twJoin } from 'tailwind-merge';
import { desktopFirstSizes } from 'utils/mediaQueries';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';
import { twMergeCustom } from 'utils/twMerge';
import { useGetWindowSize } from 'utils/ui/useGetWindowSize';
import { useDebounce } from 'utils/useDebounce';

const isBrowserPasswordManagerHovered = (e: ReactMouseEvent<HTMLDivElement, MouseEvent>) => e.relatedTarget === window;

export const MenuIconicItemUserUnauthenticated: FC = () => {
    const { t } = useTranslation();
    const { url } = useDomainConfig();
    const [registrationUrl] = getInternationalizedStaticUrls(['/registration'], url);

    const [isClicked, setIsClicked] = useState(false);
    const [isHovered, setIsHovered] = useState(false);
    const isHoveredDelayed = useDebounce(isHovered, 200);

    const { width } = useGetWindowSize();
    const isDesktop = width > desktopFirstSizes.tablet;

    return (
        <>
            <div
                className={twJoin('group lg:relative', (isClicked || isHovered) && 'z-aboveOverlay')}
                tid={TIDs.my_account_link}
                onMouseEnter={() => isDesktop && setIsHovered(true)}
                onMouseLeave={(e) => isDesktop && !isBrowserPasswordManagerHovered(e) && setIsHovered(false)}
            >
                <MenuIconicItemLink
                    className="rounded-t p-3 transition-all max-lg:hidden"
                    tid={TIDs.layout_header_menuiconic_login_link_popup}
                >
                    <div className="relative">
                        <UserIcon className="max-h-[22px] w-6" />
                        <div className="absolute -right-1 -top-1 h-[10px] w-[10px] rounded-full bg-actionPrimaryBackground" />
                    </div>
                    {t('Login')}
                </MenuIconicItemLink>

                <div className="order-2 flex h-full w-12 cursor-pointer items-center justify-center text-lg outline-none lg:hidden">
                    <div
                        onClick={() => {
                            setIsClicked(!isClicked);
                            setIsClicked(!isHovered);
                        }}
                    >
                        <div className="relative flex h-full w-full items-center justify-center text-textInverted transition-colors">
                            <UserIcon className="max-h-[22px] w-6 text-textInverted" />
                            <div className="absolute -right-1 -top-1 h-[10px] w-[10px] rounded-full bg-actionPrimaryBackground" />
                        </div>
                    </div>
                </div>

                <div
                    className={twMergeCustom(
                        'pointer-events-none absolute right-0 top-0 block min-w-auto max-w-[335px] origin-top-right rounded-xl lg:right-[-160px] lg:min-w-[740px] vl:min-w-[807px]',
                        'lg:top-full lg:transition-all',
                        'scale-50 bg-none opacity-0',
                        isHoveredDelayed && 'pointer-events-auto z-cart scale-100 bg-background opacity-100',
                        isClicked &&
                            'pointer-events-auto fixed right-0 top-0 z-aboveOverlay h-dvh scale-100 rounded-none bg-background opacity-100 transition-[right]',
                    )}
                >
                    <div className="flex flex-row justify-between m-5 lg:hidden">
                        <span className="text-base w-full text-center">{t('My account')}</span>
                        <RemoveIcon
                            className="w-4 text-borderAccent cursor-pointer"
                            onClick={() => setIsClicked(false)}
                        />
                    </div>
                    <div className="flex w-full flex-col p-5 lg:p-9 lg:flex-row gap-8">
                        <div className="mb-auto rounded-xl bg-backgroundBrand p-5 lg:p-9 text-textInverted lg:w-1/2 order-2 lg:order-1">
                            <h4>{t('Benefits of registration')}</h4>
                            <div className="my-4">
                                <p className="text-textInverted">
                                    <CheckmarkIcon className="mr-2" />
                                    {t('There may be an advantage here')}
                                </p>
                                <p className="text-textInverted">
                                    <CheckmarkIcon className="mr-2" />
                                    {t('I would write something better')}
                                </p>
                                <p className="text-textInverted">
                                    <CheckmarkIcon className="mr-2" />
                                    {t('It is not good to wait')}
                                </p>
                            </div>

                            <ExtendedNextLink
                                href={registrationUrl}
                                skeletonType="registration"
                                tid={TIDs.login_popup_register_button}
                            >
                                <Button variant="inverted">{t('Register')}</Button>
                            </ExtendedNextLink>
                        </div>
                        <div className="w-full lg:w-[364px] order-1 lg:order-2">
                            <LoginForm
                                formContentWrapperClassName={isHoveredDelayed || isClicked ? '-hidden' : 'hidden'}
                            />
                        </div>
                    </div>
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
