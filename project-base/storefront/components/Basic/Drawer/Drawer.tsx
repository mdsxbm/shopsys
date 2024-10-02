import { RemoveIcon } from 'components/Basic/Icon/RemoveIcon';
import { AnimatePresence, m } from 'framer-motion';
import { twMergeCustom } from 'utils/twMerge';

type DrawerProps = {
    title: string;
    isClicked: boolean;
    setIsClicked: (value: boolean) => void;
};

export const Drawer: FC<DrawerProps> = ({ title, isClicked, setIsClicked, children, className }) => {
    return (
        <AnimatePresence initial={false}>
            {isClicked && (
                <m.div
                    animate={{ translateX: '0%' }}
                    exit={{ translateX: '100%' }}
                    initial={{ translateX: '100%' }}
                    transition={{ duration: 0.2 }}
                    className={twMergeCustom(
                        'p-5 pointer-events-none absolute top-[-12px] right-[-15px] z-cart min-w-[315px]',
                        'bg-background top-0 right-0 rounded-none h-dvh fixed z-aboveOverlay pointer-events-auto',
                        className,
                    )}
                >
                    <div className="flex flex-row justify-between mb-10 pr-1">
                        <span className="text-base w-full text-center">{title}</span>
                        <RemoveIcon
                            className="w-4 text-borderAccent cursor-pointer"
                            onClick={() => setIsClicked(false)}
                        />
                    </div>
                    {children}
                </m.div>
            )}
        </AnimatePresence>
    );
};
