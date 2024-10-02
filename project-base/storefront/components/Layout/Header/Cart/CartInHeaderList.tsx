import { CartInHeaderListItem } from './CartInHeaderListItem';
import { EmptyCartIcon } from 'components/Basic/Icon/EmptyCartIcon';
import { LoaderWithOverlay } from 'components/Basic/Loader/LoaderWithOverlay';
import FreeTransportRange from 'components/Blocks/FreeTransport/FreeTransportRange';
import { Button } from 'components/Forms/Button/Button';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { GtmProductListNameType } from 'gtm/enums/GtmProductListNameType';
import useTranslation from 'next-translate/useTranslation';
import { useRouter } from 'next/router';
import { twJoin } from 'tailwind-merge';
import { useCurrentCart } from 'utils/cart/useCurrentCart';
import { useRemoveFromCart } from 'utils/cart/useRemoveFromCart';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';

export const CartInHeaderList: FC = () => {
    const router = useRouter();
    const { t } = useTranslation();
    const { cart } = useCurrentCart();
    const { url } = useDomainConfig();
    const [cartUrl] = getInternationalizedStaticUrls(['/cart'], url);
    const { removeFromCart, isRemovingFromCart } = useRemoveFromCart(GtmProductListNameType.cart);

    const shouldDisplayTransportBar = cart?.remainingAmountWithVatForFreeTransport !== null && cart?.items.length;

    if (!cart?.items.length) {
        return (
            <>
                <span>{t('Your cart is currently empty.')}</span>
                <EmptyCartIcon className={twJoin('w-20')} />
            </>
        );
    }
    return (
        <>
            <ul
                className={twJoin(
                    'relative w-[315px] max-h-[78dvh] m-0 flex list-none flex-col overflow-y-auto p-0',
                    'lg:w-[510px] lg:max-h-[50dvh] overflow-auto',
                )}
            >
                {isRemovingFromCart && <LoaderWithOverlay className="w-16" />}
                {cart.items.map((cartItem, listIndex) => (
                    <CartInHeaderListItem
                        key={cartItem.uuid}
                        cartItem={cartItem}
                        onRemoveFromCart={() => removeFromCart(cartItem, listIndex)}
                    />
                ))}
            </ul>
            <div className={twJoin('flex pt-5 gap-4', shouldDisplayTransportBar ? 'justify-between' : 'justify-end')}>
                <FreeTransportRange />
                <Button className="rounded-lg" size="small" onClick={() => router.push(cartUrl)}>
                    {t('Go to cart')}
                </Button>
            </div>
        </>
    );
};
