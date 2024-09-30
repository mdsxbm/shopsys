import { FlashMessageKeys } from './applicationErrors';
import { Translate } from 'next-translate';

const getErrorMessageTranslationString = (errorCode: FlashMessageKeys, t: Translate): string | undefined => {
    const ERROR_MESSAGES: Record<FlashMessageKeys, string> = {
        default: t('Unknown error.'),
        'cart-not-found': t('Cart not found.'),
        'max-allowed-limit': t('Max allowed limit reached.'),
        'packetery-address-id-invalid': t('Invalid Packetery address id.'),
        'invalid-credentials': t('Invalid credentials.'),
        'invalid-refresh-token': t('Invalid refresh token.'),
        'order-emails-not-sent': t('Automatic order emails was not sent.'),
        'order-empty-cart': t('Cart is empty.'),
        'personal-data-request-type-invalid': t('Invalid request type.'),
        'blog-category-not-found': t('Category not found.'),
        'image-type-invalid': t('Invalid image type.'),
        'order-not-found': t('Order not found.'),
        'complaint-not-found': t('Complaint not found.'),
        'personal-data-hash-invalid': t('Invalid hash.'),
        'product-price-missing': t('Product price is missing.'),
        'store-not-found': t('Store not found.'),
        'product-not-found': t('Product not found.'),
        'handling-with-logged-customer-comparison': t('Product not found.'),
        'COMPARISON-product-list-not-found': t('Comparison not found.'),
        'COMPARISON-product-not-in-list': t('Compared product not found.'),
        'COMPARISON-product-already-in-list': t('Compared product is already compared.'),
        'WISHLIST-product-list-not-found': t('Wishlist not found.'),
        'WISHLIST-product-already-in-list': t('Product in wishlist already exists.'),
        'WISHLIST-product-not-in-list': t('Product in wishlist not found.'),
        'article-not-found': t('Article not found.'),
        'cannot-remove-own-customer-user': t('Cannot delete own user'),
        'access-denied': t('Access denied'),
        'invalid-quantity': t('Complaint item quantity exceeds the order item quantity.'),
    };

    return ERROR_MESSAGES[errorCode];
};

export const getErrorMessage = (errorCode: FlashMessageKeys, t: Translate): string => {
    const translationString = getErrorMessageTranslationString(errorCode, t);

    return translationString !== undefined ? t(translationString) : t('Unknown error.');
};
