import axios from '../axios.js';

export default {
  getCart() {
    return axios.get('/cart');
  },
  addItem(productId, quantity = 1) {
    return axios.post('/cart/items', { productId, quantity });
  },
  updateItem(productId, quantity) {
    return axios.patch(`/cart/items/${productId}`, { quantity });
  },
  removeItem(productId) {
    return axios.delete(`/cart/items/${productId}`);
  },
  mergeCart(items) {
    return axios.post('/cart/merge', { items });
  },
};