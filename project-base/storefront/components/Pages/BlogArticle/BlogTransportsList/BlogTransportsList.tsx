import { useBlogTransportsQuery } from 'graphql/requests/blogCategories/queries/BlogTransportsQuery.generated';
import useTranslation from 'next-translate/useTranslation';
import { twJoin } from 'tailwind-merge';
import { useFormatPrice } from 'utils/formatting/useFormatPrice';


export const BlogTransportsList: FC = () => {
    const [{ data }] = useBlogTransportsQuery();
    const formatPrice = useFormatPrice();
    const { t } = useTranslation();

    return (
        <ul className="gjs-fulfillment-list">
            {data?.settings?.pricing.freeTransportAndPaymentPriceWithVatLimit && (
                <li className="text-green">
                    <span>
                        {t('Order above {{ price }}', {
                            price: formatPrice(parseFloat(data.settings.pricing.freeTransportAndPaymentPriceWithVatLimit)),
                        })}
                    </span>
                    <span>{t('Free')}</span>
                </li>
            )}
            {data?.transports.map((transport) => (
                <li key={transport.uuid} className={twJoin(transport.price.priceWithVat[0] === '0' && 'text-green')}>
                    <span>{transport.name}</span>
                    <span>{formatPrice(parseFloat(transport.price.priceWithVat))}</span>
                </li>
            ))}
        </ul>
    );
};