import { AnimateAppearDiv } from 'components/Basic/Animations/AnimateAppearDiv';
import { AnimatePresence } from 'framer-motion';
import { twMergeCustom } from 'utils/twMerge';

type CartInHeaderPopoverProps = {
    isHovered: boolean;
    isCartEmpty: boolean;
};

export const CartInHeaderPopover: FC<CartInHeaderPopoverProps> = ({ children, isHovered, isCartEmpty }) => {
    return (
        <AnimatePresence initial={false}>
            {isHovered && (
                <AnimateAppearDiv
                    className={twMergeCustom(
                        'absolute hidden lg:block p-5 pointer-events-auto top-[54px] right-[-15px] z-cart',
                        'min-w-[315px] origin-top-right bg-background rounded-lg right-0 h-auto',
                        isCartEmpty ? 'flex w-96 flex-nowrap items-center justify-between' : 'w-[548px]',
                    )}
                >
                    {children}
                </AnimateAppearDiv>
            )}
        </AnimatePresence>
    );
};
