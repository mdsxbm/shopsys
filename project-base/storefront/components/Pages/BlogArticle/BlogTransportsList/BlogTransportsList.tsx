/* TODO FE
import { useBlogTransportsQueryApi } from 'graphql/generated';
import useTranslation from 'next-translate/useTranslation';
import { twJoin } from 'tailwind-merge';
import { useFormatPrice } from 'utils/formatting/useFormatPrice';

export const BlogTransportsList: FC = () => {
    const [{ data }] = useBlogTransportsQueryApi();
    const formatPrice = useFormatPrice();
    const { t } = useTranslation();

    return (
        <ul className="gjs-fulfillment-list">
            {data?.freeFulfillmentLimitWithVat && (
                <li className="text-green">
                    <span>
                        {t('Order above {{ price }}', {
                            price: formatPrice(parseFloat(data.freeFulfillmentLimitWithVat)),
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
*/
