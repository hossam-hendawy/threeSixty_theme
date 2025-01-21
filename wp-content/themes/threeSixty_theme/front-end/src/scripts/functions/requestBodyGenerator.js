export const requestBodyGenerator = (btn) => {
  const {template, action, args} = btn.dataset
  const data = new FormData();
  data.append('args', args);
  data.append('template', template);
  data.append('_ajax_nonce', theme_ajax_object._ajax_nonce);
  data.append('action', action ? action : 'more_posts');
  return data;
};
