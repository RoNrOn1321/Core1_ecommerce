<?php
$page_title = "Reviews";
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Reviews & Ratings</h1>
                <p class="text-gray-600">Manage customer feedback and improve your store reputation</p>
            </div>

            <!-- Review Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900">4.7</p>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-thumbs-up text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Reviews</p>
                            <p class="text-2xl font-bold text-gray-900">287</p>
                            <p class="text-sm text-green-600">+15 this week</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Pending Responses</p>
                            <p class="text-2xl font-bold text-gray-900">12</p>
                            <p class="text-sm text-red-600">Needs attention</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Response Rate</p>
                            <p class="text-2xl font-bold text-gray-900">94%</p>
                            <p class="text-sm text-green-600">Excellent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Rating Distribution</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-sm w-8">5★</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">187</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm w-8">4★</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">57</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm w-8">3★</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: 10%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">28</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm w-8">2★</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: 3%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">9</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm w-8">1★</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: 2%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">6</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Reviewed Products</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=50&h=50&fit=crop" alt="Product" class="w-12 h-12 rounded object-cover">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Premium Sneakers</p>
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400 text-sm">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span class="text-sm text-gray-600 ml-1">(89 reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-beige font-semibold">4.9</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=50&h=50&fit=crop" alt="Product" class="w-12 h-12 rounded object-cover">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Classic Watch</p>
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400 text-sm">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="text-sm text-gray-600 ml-1">(67 reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-beige font-semibold">4.6</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=50&h=50&fit=crop" alt="Product" class="w-12 h-12 rounded object-cover">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Designer Sunglasses</p>
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400 text-sm">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span class="text-sm text-gray-600 ml-1">(43 reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-beige font-semibold">4.2</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Reviews</h3>
                        <div class="flex gap-2">
                            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                <option>All Reviews</option>
                                <option>5 Stars</option>
                                <option>4 Stars</option>
                                <option>3 Stars</option>
                                <option>2 Stars</option>
                                <option>1 Star</option>
                            </select>
                            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                <option>All Products</option>
                                <option>Premium Sneakers</option>
                                <option>Classic Watch</option>
                                <option>Designer Sunglasses</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <!-- Review 1 -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bc?w=50&h=50&fit=crop&crop=face" alt="Customer" class="w-12 h-12 rounded-full">
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Sarah Johnson</h4>
                                    <p class="text-sm text-gray-600">Verified Purchase • 2 days ago</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600">5.0</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Product: <span class="font-medium text-gray-900">Premium Sneakers</span></p>
                            <p class="text-gray-700">"Absolutely love these sneakers! They're incredibly comfortable and the quality is outstanding. Perfect for daily wear and they look great with any outfit. Highly recommend!"</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="text-gray-500 hover:text-gray-700 flex items-center">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <span class="text-sm">Helpful (12)</span>
                                </button>
                                <button class="text-beige hover:text-beige-dark">
                                    <i class="fas fa-reply mr-1"></i>
                                    <span class="text-sm">Reply</span>
                                </button>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Responded
                            </span>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face" alt="Customer" class="w-12 h-12 rounded-full">
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Mike Chen</h4>
                                    <p class="text-sm text-gray-600">Verified Purchase • 1 week ago</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600">4.0</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Product: <span class="font-medium text-gray-900">Classic Watch</span></p>
                            <p class="text-gray-700">"Good watch overall. The design is elegant and it keeps time well. Only minor complaint is the strap could be a bit more comfortable, but otherwise very satisfied with the purchase."</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="text-gray-500 hover:text-gray-700 flex items-center">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <span class="text-sm">Helpful (8)</span>
                                </button>
                                <button class="text-beige hover:text-beige-dark">
                                    <i class="fas fa-reply mr-1"></i>
                                    <span class="text-sm">Reply</span>
                                </button>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face" alt="Customer" class="w-12 h-12 rounded-full">
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Emma Davis</h4>
                                    <p class="text-sm text-gray-600">Verified Purchase • 2 weeks ago</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600">2.0</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Product: <span class="font-medium text-gray-900">Designer Sunglasses</span></p>
                            <p class="text-gray-700">"Unfortunately, these sunglasses didn't meet my expectations. The frame feels a bit flimsy and the lenses scratch easily. Expected better quality for the price point."</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="text-gray-500 hover:text-gray-700 flex items-center">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <span class="text-sm">Helpful (3)</span>
                                </button>
                                <button class="text-beige hover:text-beige-dark">
                                    <i class="fas fa-reply mr-1"></i>
                                    <span class="text-sm">Reply</span>
                                </button>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Needs Response
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing 1 to 3 of 287 reviews
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                        <button class="px-3 py-2 text-sm bg-beige text-white rounded-lg">1</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>