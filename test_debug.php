
$user = User::find(1);
echo "Total products: " . $user->products()->count() . "\n";
echo "Total cards: " . $user->cards()->count() . "\n";

$products = $user->products()->limit(3)->get();
$products->each(function($p) {
  echo sprintf("Product %d: title=%s, image_size=%d\n", 
    $p->id, 
    $p->title, 
    strlen($p->image ?? '')
  );
});
